<?php
if (! function_exists('getjpegsize')) {
    
    // Retrieve JPEG width and height without downloading/reading entire image.
    
    /**
     * As noted below, getimagesize will download the entire image before 
     * it checks for the requested information.This is extremely slow on 
     * large images that are accessed remotely. Since the width/height is 
     * in the first few bytes of the file, there is no need to download the 
     * entire file. I wrote a function to get the size of a JPEG by streaming 
     * bytes until the proper data is found to report the width and height:
     * 
     * @param unknown $img_loc            
     * @return multitype:number |boolean
     */
    function getjpegsize($img_loc)
    {
        $handle = fopen($img_loc, "rb") or die("Invalid file stream.");
        $new_block = NULL;
        if (! feof($handle)) {
            $new_block = fread($handle, 32);
            $i = 0;
            if ($new_block[$i] == "\xFF" && $new_block[$i + 1] == "\xD8" && $new_block[$i + 2] == "\xFF" && $new_block[$i + 3] == "\xE0") {
                $i += 4;
                if ($new_block[$i + 2] == "\x4A" && $new_block[$i + 3] == "\x46" && $new_block[$i + 4] == "\x49" && $new_block[$i + 5] == "\x46" && $new_block[$i + 6] == "\x00") {
                    // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                    $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                    $block_size = hexdec($block_size[1]);
                    while (! feof($handle)) {
                        $i += $block_size;
                        $new_block .= fread($handle, $block_size);
                        if ($new_block[$i] == "\xFF") {
                            // New block detected, check for SOF marker
                            $sof_marker = array(
                                "\xC0",
                                "\xC1",
                                "\xC2",
                                "\xC3",
                                "\xC5",
                                "\xC6",
                                "\xC7",
                                "\xC8",
                                "\xC9",
                                "\xCA",
                                "\xCB",
                                "\xCD",
                                "\xCE",
                                "\xCF"
                            );
                            if (in_array($new_block[$i + 1], $sof_marker)) {
                                // SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
                                $size_data = $new_block[$i + 2] . $new_block[$i + 3] . $new_block[$i + 4] . $new_block[$i + 5] . $new_block[$i + 6] . $new_block[$i + 7] . $new_block[$i + 8];
                                $unpacked = unpack("H*", $size_data);
                                $unpacked = $unpacked[1];
                                $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                                $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                                return array(
                                    $width,
                                    $height
                                );
                            } else {
                                // Skip block marker and read block size
                                $i += 2;
                                $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                                $block_size = hexdec($block_size[1]);
                            }
                        } else {
                            return FALSE;
                        }
                    }
                }
            }
        }
        return FALSE;
    }
}

if (! function_exists('equal_ratio_thumb')) {

    /**
     * 生成等比缩略图
     * 
     * @param unknown $imgPath
     *            图片全路径
     * @param number $maxSize
     *            最大尺寸
     * @param string $savePath
     *            缩略图保存全路径
     * @param string $cover
     *            是否覆盖
     * @return boolean|Ambigous <string, unknown>
     */
    function equal_ratio_thumb($imgPath, $maxSize = 80, $savePath = '', $cover = false)
    {
        if (file_exists($imgPath)) {
            try {
                $pathinfo = pathinfo($imgPath);
                $path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . "_{$maxSize}." . $pathinfo['extension'];
                $path = $savePath ? $savePath : $path;
                if (! $cover && file_exists($path)) {
                    return $path;
                }
                equal_ratio_resize($imgPath, $path, $maxSize);
            } catch (\Exception $e) {
                \Log::error($e);
                return false;
            }
            return $path;
        }
        return false;
    }
}

if (! function_exists('equal_ratio_resize')) {

    /**
     * 等比缩放图片
     * 
     * @param unknown $path            
     * @param unknown $dst_path            
     * @param unknown $max            
     * @throws \Exception
     */
    function equal_ratio_resize($path, $dst_path, $max)
    {
        ini_set('memory_limit', '512M');
        $info = @getimagesize($path);
        
        if ($info === false) {
            throw new \Exception("Unable to read image from file ({$path}).");
        }
        
        // define core
        switch ($info[2]) {
            case IMAGETYPE_PNG:
                $core = imagecreatefrompng($path);
                break;
            case IMAGETYPE_JPEG:
                $core = imagecreatefromjpeg($path);
                break;
            default:
                throw new \Exception("Unable to read image type. GD driver is only able to decode JPG, PNG or GIF files.");
        }
        
        $resource = & $core;
        
        $width = imagesx($resource);
        $height = imagesy($resource);
        if ($width > $height) { // 等比尺寸
            $dst_h = intval($height * $max / $width);
            $dst_w = $max;
        } else {
            $dst_w = intval($width * $max / $height);
            $dst_h = $max;
        }
        // new canvas
        $canvas = imagecreatetruecolor($width, $height);
        
        // fill with transparent color
        imagealphablending($canvas, false);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
        imagecolortransparent($canvas, $transparent);
        imagealphablending($canvas, true);
        
        // copy original
        imagecopy($canvas, $resource, 0, 0, 0, 0, $width, $height);
        imagedestroy($resource);
        
        $resource = $canvas;
        
        // create new image
        $modified = imagecreatetruecolor($dst_w, $dst_h);
        
        // preserve transparency
        $transIndex = imagecolortransparent($resource);
        
        if ($transIndex != - 1) {
            $rgba = imagecolorsforindex($modified, $transIndex);
            $transColor = imagecolorallocatealpha($modified, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
            imagefill($modified, 0, 0, $transColor);
            imagecolortransparent($modified, $transColor);
        } else {
            imagealphablending($modified, false);
            imagesavealpha($modified, true);
        }
        
        // copy content from resource
        $result = imagecopyresampled($modified, $resource, 0, 0, 0, 0, $dst_w, $dst_h, $width, $height);
        $resource = $modified;
        
        switch (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            case 'png':
            case 'image/png':
            case 'image/x-png':
                ob_start();
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                imagepng($resource, null, - 1);
                $mime = image_type_to_mime_type(IMAGETYPE_PNG);
                $buffer = ob_get_contents();
                ob_end_clean();
                break;
            
            case 'jpg':
            case 'jpeg':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                ob_start();
                imagejpeg($resource, null, 100);
                $mime = image_type_to_mime_type(IMAGETYPE_JPEG);
                $buffer = ob_get_contents();
                ob_end_clean();
                break;
            default:
                throw new \Exception("Encoding format is not supported.");
        }
        $saved = @file_put_contents($dst_path, $buffer);
        if ($saved === false) {
            throw new \Exception("Can't write image data to path ({$dst_path})");
        }
    }
}


