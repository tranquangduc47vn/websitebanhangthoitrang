<?php
class Upload_library
{
    protected $CI;
    protected $last_error = '';

    function __construct()
    {
        $this->CI =& get_instance();
    }

    function last_error()
    {
        return $this->last_error;
    }

    protected function set_error($message)
    {
        $this->last_error = trim((string) $message);
    }

    function upload($upload_path = '', $image = '')
    {
        $this->last_error = '';
        $config = $this->config($upload_path);

        $this->CI->upload->initialize($config);

        if ($this->CI->upload->do_upload($image))
        {
            $data = $this->CI->upload->data();
            return $data['file_name'];
        }

        $this->set_error(strip_tags($this->CI->upload->display_errors('', '')));
        if ($this->last_error === '') {
            $this->set_error('Không thể tải ảnh lên. Chỉ hỗ trợ JPG, PNG, GIF, WEBP (tối đa 5MB).');
        }

        return false;
    }

    function upload_file($upload_path = '', $list_image = '')
    {
        $this->last_error = '';
        $config = $this->config($upload_path);

        $image_list = array();

        if (empty($_FILES[$list_image]['name']) || ! is_array($_FILES[$list_image]['name'])) {
            return $image_list;
        }

        $file = $_FILES[$list_image];
        $count = count($file['name']);

        for ($i = 0; $i < $count; $i++)
        {
            if (empty($file['name'][$i])) {
                continue;
            }

            $_FILES['userfile']['name']     = $file['name'][$i];
            $_FILES['userfile']['type']     = $file['type'][$i];
            $_FILES['userfile']['tmp_name'] = $file['tmp_name'][$i];
            $_FILES['userfile']['error']    = $file['error'][$i];
            $_FILES['userfile']['size']     = $file['size'][$i];

            $this->CI->upload->initialize($config);

            if ($this->CI->upload->do_upload())
            {
                $data = $this->CI->upload->data();
                $image_list[] = $data['file_name'];
            }
            else
            {
                $err = strip_tags($this->CI->upload->display_errors('', ''));
                if ($err !== '') {
                    $this->set_error($err);
                }
            }
        }

        return $image_list;
    }

    function config($upload_path = '')
    {
        $config = array();

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|jpe|jfif|png|webp|bmp';
        $config['max_size']      = 5120; // 5MB
        $config['file_ext_tolower'] = TRUE;

        return $config;
    }
}
