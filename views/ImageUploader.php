<?php

class ImageUploader {
    private $target_dir;
    private $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];

    public function __construct($target_dir = "uploads/") {
        $this->target_dir = $target_dir;
    }

    /**
     * Handles the file upload process.
     *
     * @param array $file The file array from $_FILES.
     * @return string|false The new filename on success, false on failure.
     */
    public function upload(array $file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            // No file uploaded or an error occurred, but not necessarily a failure state for the whole form.
            return false;
        }

        if (!is_dir($this->target_dir) && !mkdir($this->target_dir, 0755, true)) {
            $_SESSION['error'] = 'Failed to create upload directory.';
            return false;
        }

        $image_name = time() . '_' . basename($file["name"]);
        $target_file = $this->target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = 'File is not a valid image.';
            return false;
        }

        if (!in_array($imageFileType, $this->allowed_types)) {
            $_SESSION['error'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            return false;
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $image_name;
        }
        
        $_SESSION['error'] = 'Sorry, there was an error moving the uploaded file.';
        return false;
    }

    /**
     * Deletes an image file from the upload directory.
     *
     * @param string|null $filename The name of the file to delete.
     * @return bool True on success or if no file is provided, false on failure.
     */
    public function delete($filename) {
        if (empty($filename)) return true;
        $file_path = $this->target_dir . $filename;
        return file_exists($file_path) ? unlink($file_path) : true;
    }
}