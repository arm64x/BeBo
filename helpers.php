<?php
// helpers.php
class FileStorage {
    private $jsonFile;
    private $data;
    
    public function __construct($jsonFile) {
        $this->jsonFile = $jsonFile;
        $this->loadData();
    }
    
    private function loadData() {
        if (file_exists($this->jsonFile)) {
            $content = file_get_contents($this->jsonFile);
            $this->data = json_decode($content, true) ?: [];
        } else {
            $this->data = [];
            $this->saveData();
        }
    }
    
    private function saveData() {
        $jsonData = json_encode($this->data, JSON_PRETTY_PRINT);
        if ($jsonData === false) {
            throw new Exception("Failed to encode JSON data");
        }
        
        if (file_put_contents($this->jsonFile, $jsonData) === false) {
            throw new Exception("Failed to write to JSON file");
        }
    }
    
    public function addFile($fileInfo) {
        $this->data[] = $fileInfo;
        $this->saveData();
        return true;
    }
    
    public function getFileByName($filename) {
        foreach ($this->data as $file) {
            if ($file['filename'] === $filename) {
                return $file;
            }
        }
        return null;
    }
    
    public function deleteExpiredFiles($currentTime) {
        $expired = array_filter($this->data, function($file) use ($currentTime) {
            return $file['expiry_time'] <= $currentTime;
        });
        
        $this->data = array_filter($this->data, function($file) use ($currentTime) {
            return $file['expiry_time'] > $currentTime;
        });
        
        $this->saveData();
        return $expired;
    }
}
