<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Normalize MIME on Windows; fallback getimagesize() when mimes.php rejects.
class MY_Upload extends CI_Upload {

	public function is_allowed_filetype($ignore_mime = FALSE)
	{
		$png_mimes = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($this->file_type, $png_mimes, TRUE)) {
			$this->file_type = 'image/png';
		} elseif (in_array($this->file_type, $jpeg_mimes, TRUE)) {
			$this->file_type = 'image/jpeg';
		}

		if (parent::is_allowed_filetype($ignore_mime)) {
			return TRUE;
		}

		if ($ignore_mime === TRUE) {
			return FALSE;
		}

		$ext = strtolower(ltrim((string) $this->file_ext, '.'));
		if ($ext === '' || ! is_array($this->allowed_types) || ! in_array($ext, $this->allowed_types, TRUE)) {
			return FALSE;
		}

		// Valid extension + readable image — accept despite MIME mismatch on Windows.
		return @getimagesize($this->file_temp) !== FALSE;
	}
}
