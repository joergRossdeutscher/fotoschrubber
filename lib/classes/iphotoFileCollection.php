<?php

/**
 * Class iphotoFileCollection
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class iphotoFileCollection extends fileCollection
{

    /**
     * @param $mediathekFile
     * @return array
     */
    function getVolumeListInIPhotoDb($mediathekFile)
    {
        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');

        $result = $db->query('SELECT uuid,name FROM RKVolume');

        $volume = array();
        while ($row = $result->fetchArray()) {
            $volume[$row['uuid']] = $row['name'];
        }
        $db->closeDb();
        return $volume;
    }

    /**
     * @param $mediathekFile
     * @return array
     */
    function getAlbumListInIPhotoDb($mediathekFile)
    {
        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');

        $result = $db->query('SELECT modelId,folderUuid,name FROM RKAlbum');

        $album = array();
        while ($row = $result->fetchArray()) {
#            if( $row['name']=="") {
#                echo "EMPTYNAME {$row['name']}\n";
#            }
#            $album[$row['folderUuid']] = preg_replace('/\W/uis' , '_' , $row['modelId'] . '-' . $row['name']);
            $album[$row['folderUuid']] = preg_replace('/\W/uis', '_', $row['name']);
        }
        $db->closeDb();
        #print_r($album);exit;
        return $album;
    }

    /**
     * @param $mediathekFile
     * @return array
     */
    function getFolderListInIPhotoDb($mediathekFile)
    {
        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');

        $result = $db->query('SELECT modelId,uuid,name FROM RKFolder');

        $folder = array();
        while ($row = $result->fetchArray()) {
            $folder[$row['uuid']] = $row['modelId'] . '-' . preg_replace('/\W/uis', '_', $row['name']);
        }
        $db->closeDb();
        #print_r($folder);exit;
        return $folder;
    }

    /**
     * @var
     */
    public $fotofolder;

    /**
     * @param $makeType
     * @param $mediathekFile
     * @return $this
     */
    function getFilesInIPhotoDb($makeType, $mediathekFile)
    {
        if (!is_dir($mediathekFile)) {
            die("Cannot find $mediathekFile\n");
        }

        $volume = $this->getVolumeListInIPhotoDb($mediathekFile);
#        $album = $this->getAlbumListInIPhotoDb($mediathekFile);
        $fotofolder = $this->getFolderListInIPhotoDb($mediathekFile);

        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');

        $result = $db->query('SELECT * FROM RKMaster');

        while ($row = $result->fetchArray()) {
#print_r($row);exit;

            $row['fullpath'] = '/Volumes/' . $volume[$row['fileVolumeUuid']] . '/' . $row['imagePath'];

            preg_match('/^(.*)\/(.*)$/uis', $row['fullpath'], $splitname);

            $tmp = new $makeType;
            $tmp->modelId = $row['modelId'];

            $tmp->projectUuid = $row['projectUuid'];
            $tmp->fotofolder = $fotofolder[$row['projectUuid']];

            #if ($fotofolder[$row['projectUuid']] == "") {
            #   echo "EMPTYNAME {$row['imagePath']}\n";
            #  die(-1);
#                $this->beautyfulPrintr($row);
#                $this->debugquery($db, "SELECT * from RKVersion WHERE uuid='".$row["originalVersionUuid"]."'");
            # exit;
            # }

            $tmp->volumeId = $row['fileVolumeUuid'];
            $tmp->volume = $volume[$row['fileVolumeUuid']];

            $tmp->imagePath = $row['imagePath'];
            $tmp->folder = $splitname[1];
            $tmp->filename = $splitname[2];
            $this->file[] = $tmp;
            unset($tmp);
        }
        $db->closeDb();

        return $this;
    }

    /**
     * @param $db
     * @param $query
     */
    function debugquery($db, $query)
    {
        echo "=========={$query}==========\n";
        $result = $db->query($query);

        while ($row = $result->fetchArray()) {
            $this->beautyfulPrintr($row);
            echo "-----------------\n";
        }
        echo "========== / {$query}==========\n";
    }

    /**
     * @param $aArray
     */
    function beautyfulPrintr($aArray)
    {
        foreach ($aArray as $key => $val) {
            if (
                !is_numeric($key) &&
                $key != 'colorSpaceDefinition'
            ) {
                echo "{$key}->{$val}\n";
            }
        }
        echo "-------------------------\n\n";
    }


    /**
     * @param $target
     */
    function moveFiles($target)
    {
        foreach ($this->file as $file) {
            $sourceFile = $file->folder . '/' . $file->filename;
            $targetDir = $target . '/' . $file->fotofolder . '/';
            $targetFile = $targetDir . $file->filename;

            # mkdir
            if (!file_exists($targetDir)) {
                $this->shell( "mkdir -p " .
                    escapeshellarg($targetDir . "_misc")
                );
            }

            # cp _jpg
            $this->shell("cp -a " .
                escapeshellarg($sourceFile) . " " .
                escapeshellarg($targetDir)
            );

            # cp _misc
            $pathinfo = pathinfo($sourceFile);
            $sourceFilePattern = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
            foreach (glob($sourceFilePattern . ".*") as $miscFilename) {
                if ($miscFilename != $sourceFile) {
                    $this->shell("cp -a " .
                        escapeshellarg($miscFilename) . " " .
                        escapeshellarg($targetDir.'_misc/')
                    );
                }
            }

            # Fix DB
            $query = "UPDATE RKMaster SET imagePath='{$targetFile}' where modelId='" . $file->modelId . "' LIMIT 1";
            #echo "$query\n\n";
        }
    }

    /**
     * @param $cmd
     */
    function shell($cmd) {
        echo "{$cmd}\n";
        echo `{$cmd}`;
    }

}
