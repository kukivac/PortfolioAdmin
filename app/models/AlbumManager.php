<?php


namespace app\models;


use Exception;
use PDOException;

class AlbumManager
{
    public function albumExists($albumTitle)
    {
        return DbManager::requestAffect("SELECT title FROM album WHERE title=?", [$albumTitle]) == 1;
    }

    /**
     * @param $values
     *
     * @return bool|Exception|PDOException
     * @throws Exception
     */
    public function createAlbum($values)
    {
        var_dump($values);

        if (DbManager::requestAffect("SELECT title FROM album WHERE title=?", [$values["albumTitle"]]) > 0) {
            throw new Exception("Name already exists");
        }
        return DbManager::requestInsert('
            INSERT INTO album (id, title, dash_title, description, keywords, no_photos, added, edited, `order`, visible, cover_photo) 
            VALUES(Null,?,?,?,?,0,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1,Null)
            ', [$values["albumTitle"], $values["albumDashtitle"], $values["albumDescription"], $values["albumKeywords"]]);


    }

    /**
     * @param $dashtitle
     *
     * @return array|false|void
     */
    public function getAlbumInfo($dashtitle)
    {
        if (DbManager::requestAffect("SELECT dash_title FROM album WHERE dash_title=?", [$dashtitle]) === 1) {
            $album = DbManager::requestSingle("SELECT * FROM album WHERE album.dash_title=?", [$dashtitle]);
            $images = DbManager::requestMultiple("SELECT * FROM image WHERE album_id = ?", [$album["id"]]);
            $album["images"] = $images;
            return $album;
        } else {
            return false;
        }
    }

    /**
     * @param $values
     *
     * @return Exception|void
     */
    public function editAlbum($values)
    {
        if ($this->albumExists($values["oldDashtitle"]) || $this->albumExists($values["albumDashtitle"])) {
            DbManager::requestInsert("UPDATE album SET title = ?, dash_title = ?, description = ?, keywords = ?, edited = CURRENT_TIMESTAMP, visible = ? WHERE dash_title = ?",
                [$values["albumTitle"], $values["albumDashtitle"], $values["albumDescription"], $values["albumKeywords"], $values["albumVisible"], $values["oldDashtitle"]]);
        } else {
            return new Exception;
        }
    }

    public function getAllAlbums()
    {
        $albums = DbManager::requestMultiple("SELECT id,title,dash_title,cover_photo,no_photos,visible FROM album");
        $newAlbums = array();
        foreach ($albums as $album) {
            if ($album["cover_photo"] == Null) {
                $album["cover_photo"] = DbManager::requestUnit("SELECT filename FROM image WHERE album_id = ? ORDER BY id LIMIT 1", [$album["id"]]);
            } else {
                $album["cover_photo"] = DbManager::requestUnit("SELECT filename FROM image WHERE id = ?", [$album["cover_photo"]]);
            }
            array_push($newAlbums, $album);
        }
        return $newAlbums;
    }

    /**
     * @param array  $images
     * @param string $albumTitle
     *
     * @return void
     */
    public function uploadImages(array $images, string $albumTitle): void
    {
        $albumId = DbManager::requestUnit("SELECT id FROM album WHERE dash_title = ?", [$albumTitle]);
        for ($i = 0; $i < sizeof($images["filenames"]); $i++) {
            DbManager::requestInsert("INSERT INTO image(filename, data_type, added, edited, title, description, `order`, album_id) 
                                      VALUES(?,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,?,'',0,?)",
                [$images["filenames"][$i], explode(".", $images["filenames"][$i])[1], explode(".", $images["file-names"][$i])[0], $albumId]);
        }

    }

    public function getAlbumImages($title)
    {
        $albumId = DbManager::requestUnit("SELECT id FROM album WHERE dash_title = ?", [$title]);
        return DbManager::requestMultiple("SELECT * FROM image WHERE album_id = ?",[$albumId]);
    }

}