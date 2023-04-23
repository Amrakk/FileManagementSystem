<?php
    function callApi($url, $data=null, $method) 
    {

        $curl = curl_init();
        $method = strtoupper($method);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        switch($method) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                if ($data != null) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                if ($data != null) {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                return array('code' => 20, 'message' => 'Unsupported method');
        }

        $response = curl_exec($curl);
        
        if (curl_errno($curl)) 
            return array('code' => 10,'message' => curl_error($curl));
        
        curl_close($curl);
        
        $data = json_decode($response, true);
        return $data;
    }

    function loadUserFiles($file_list)
    {
        $load_file_list = array();

        if(count($file_list) == 0) return $load_file_list;
        else if(count($file_list) == 1 && $file_list[0]['type'] == 'file' && isset($file_list[0]['data'])) {
            return $file_list[0]['data'];
        }

        foreach($file_list as $file)
        {
            $info = $file['info'];

            $load_file_list[] = array(
                'name' => $info['name'],
                'type' => getFileType($info['extension'] ?? ''),
                'size'=> $info['size'],
                'modified_date' => $info['modified_date'],
                'icon' => getFileIcon($info['extension'] ?? ''),
            );
        }
        return $load_file_list;
    }

    function getFileIcon($ext)
    {
        if($ext == '') return 'fas fa-folder';
        if($ext == 'pdf') return 'fas fa-file-pdf';
        if($ext == 'txt') return 'fas fa-file-alt';
        if($ext == 'mp3') return 'fas fa-file-audio';
        if($ext == 'docx' || $ext == 'doc') return 'fas fa-file-word';
        if($ext == 'zip' || $ext == 'rar') return 'fas fa-file-archive';
        if($ext == 'mp4' ||  $ext == 'mov' || $ext == 'mkv') return 'fas fa-file-video';
        if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') return 'fas fa-file-image';
        if($ext == 'php' || $ext == 'js' || $ext == 'html' || $ext == 'css') return 'fas fa-file-code';

        return 'fas fa-file';
    }
    function getFileType($ext)
    {
        if($ext == '') return 'Folder';
        if($ext == 'pdf') return 'PDF';
        if($ext == 'txt') return 'Text';
        if($ext == 'mp3') return 'Audio';
        if($ext == 'docx' || $ext == 'doc') return 'Word';
        if($ext == 'zip' || $ext == 'rar') return 'Archive';
        if($ext == 'mp4' ||  $ext == 'mov' || $ext == 'mkv') return 'Video';
        if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') return 'Image';
        if($ext == 'php' || $ext == 'js' || $ext == 'html' || $ext == 'css') return 'Code';

        return 'File';
    }
?>

