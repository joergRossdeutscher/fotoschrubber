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

        $result = $db->query('SELECT * FROM RKFolder ORDER by modelId');

        $folder = array();
        while ($row = $result->fetchArray()) {
#            $folder[$row['uuid']] = $row['modelId'] . '-' . preg_replace('/\W/uis', '_', $row['name']);
            $folder[$row['uuid']] = $this->serializeFoldername(preg_replace('/\W/uis', '_', $row['name']), $row['modelId']);
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
            if (!isset($fotofolder[$row['projectUuid']])) {
                #print_r($fotofolder);
                echo "Cannot interprete folder " . $row['projectUuid'] . " of RKMaster " . $row['modelId'] . "\n";
                #die();
            }
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
     * @param $filename
     * @param $serial
     */
    function serializeFilename($filename, $serial)
    {
        $filename = preg_replace('/___\d+\./uis', '.', $filename);
        $filename = preg_replace('/^(.*)\.(.*)$/uis', '\1' . '___' . $serial . '.\2', $filename);
        return $filename;
    }

    /**
     * @param $foldername
     * @param $serial
     * @return mixed|string
     */
    function serializeFoldername($foldername, $serial)
    {
        $foldername = preg_replace('/^d+___\./uis', '', $foldername);
        $foldername = sprintf('%012d___%s', $serial, $foldername);
        return $foldername;
    }


    /**
     * @param $mediathekFile
     * @param $target
     */
    function moveFiles($mediathekFile, $target)
    {
        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');
        foreach ($this->file as $file) {
            $sourceFile = $file->folder . '/' . $file->filename;
            #          if(preg_match('/2010_04_11/uis' , $file->folder)) {
            $targetDir = $target . '/' . $file->fotofolder . '/';
            $targetFileName = $this->serializeFilename($file->filename, $file->modelId);
            $targetFile = $targetDir . $targetFileName;
            $targetFileNoVolume = preg_replace('/^\/(.*?)\/(.*?)\//uis', '', $targetFile);

            if (!file_exists($sourceFile)) {
                echo "File cannot be found: {$sourceFile}\n";
            } else {

                # mkdir
                if (!file_exists($targetDir)) {
                    $this->shell("mkdir -p " .
                        escapeshellarg($targetDir . "_misc")
                    );
                }

                # cp _jpg
#            $this->shell("cp -a " .
                $this->shell("mv " .
                    escapeshellarg($sourceFile) . " " .
                    escapeshellarg($targetFile)
                );

                # cp _misc
                $pathinfo = pathinfo($sourceFile);
                $sourceFilePattern = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
                foreach (glob($sourceFilePattern . ".*") as $miscFilename) {
                    if ($miscFilename != $sourceFile) {
                        $targetMiscFilename = $this->serializeFilename(substr($miscFilename, mb_strlen($pathinfo['dirname']) + 1), $file->modelId);
#                    die("$targetMiscFilename\n$miscFilename\n");
#                        $this->shell("cp -a " .
                        $this->shell("mv " .
                            escapeshellarg($miscFilename) . " " .
                            escapeshellarg($targetDir . '_misc/' . $targetMiscFilename)
                        );
                    }
                }

                # Fix DB
                $query = "UPDATE RKMaster SET imagePath='{$targetFileNoVolume}' , fileName='{$targetFileName}' where modelId='" . $file->modelId . "'";
                echo "$query\n\n";
                $result = $db->exec($query);
                #           }
            }
        }
        $db->closeDb();
    }

    /**
     * @param $cmd
     */
    function shell($cmd)
    {
        echo "{$cmd}\n";
        echo `{$cmd}`;
    }


    /**
     * @param $mediathekFile
     */
    function checkConsistency($mediathekFile)
    {
        $db = new sqliteDb($mediathekFile . '/Database/apdb/Library.apdb');

        $alreadyFound = array();
        foreach ($this->file as $imgFile) {
            $file = mb_strtolower($imgFile->folder . '/' . $imgFile->filename, 'UTF-8');
            if (!file_exists($file)) {
                echo "File does not exist: {$file}\n";
            }
            if (isset($alreadyFound[$file])) {
                echo "File is duplicate ({$imgFile->modelId}/{$alreadyFound[$file]}): {$file}\n";
                $query = "DELETE FROM RKMaster WHERE modelId={$imgFile->modelId}";
                echo "$query\n\n";
                #$result = $db->exec($query);

            }
            $alreadyFound[$file] = $imgFile->modelId;
        }
        $db->closeDb();
    }
}
