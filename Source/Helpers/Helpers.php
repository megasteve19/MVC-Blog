<?php
    function DoubleQuote(string $String)
    {
        return str_replace("'", "''", $String);
    }

    /**
     * Generates random string.
     * 
     * @param int $Lenght [Optional] Lenght of random string.
     * @param string Generated string.
     * 
     * @since 1.0.0
     */
    function RandomString(int $Lenght = 16)
    {
        $Chars = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
        $String = "";

        for($i=0; $i < $Lenght; $i++)
        {
            $String .= $Chars[rand(0, count($Chars) - 1)];
        }

        return $String;
    }

    function ExtractDOM(string $DocumentString)
    {
        $Document = new DOMDocument();
        $Document->loadHTML('<?xml encoding="utf-8"?>' . $DocumentString);
        $Content = new DOMDocument();

        foreach($Document->getElementsByTagName("body")->item(0)->childNodes as $Node)
        {
            $Content->appendChild($Content->importNode($Node, true));
        }

        return $Content;
    }

    function FormatDate(string $Date, int $Hours = null)
    {
        return date("d m Y", strtotime($Date));
    }

    function TruncateString(string $String, int $Max)
    {
        if(strlen($String) > $Max)
        {
            return mb_substr($String, 0, $Max);
        }
        return $String;
    }

    ## Image area ##

    /**
     * Converts base64 string into GD image.
     * 
     * @param string $Base64 Base64 string of image.
     * @return GdImage Converted image.
     * 
     * @since 1.0.0
     */
    function Base64ToGDImage(string $Base64)
    {
        $Explode = explode(",", $Base64);
        if(count($Explode) == 1)
        {
            $Base64 = $Explode[0];
        }
        else
        {
            $Base64 = $Explode[1];
        }
        $Base64 = base64_decode($Base64);
        return imagecreatefromstring($Base64);
    }

    function ImageToBase64(string $Image)
    {
        $Image = file_get_contents("$Image.jpeg");
        $Base64 = base64_encode($Image);
        return $Base64;
    }

    /**
     * Resises image if necessary by given width.
     * 
     * @param GdImage $Image Source image to resize.
     * @param int $NewHeight New size of image.
     * @return GdImage
     * 
     * @since 1.0.0
     */
    function ResizeImage(GdImage $Image, int $NewWidth)
    {
        $ImageX = imagesx($Image);
        $ImageY = imagesy($Image);

        if($ImageX > $NewWidth)
        {
            $NewHeight = ($NewWidth / $ImageX) * $ImageY;
            $NewImage = imagecreatetruecolor($NewWidth, $NewHeight);

            imagecopyresampled($NewImage, $Image, 0, 0, 0, 0, $NewWidth, $NewHeight, $ImageX, $ImageY);

            return $NewImage;
        }

        return $Image;
    }

    /**
     * Gets image as base64 string.
     * 
     * @param string $Name Name of the image file.
     * @return string Base64 image.
     * 
     * @since 1.0.0
     */
    function GetImageAsBase64(string $Name, string $Directory)
    {
        return base64_encode(file_get_contents($Directory . "$Name.jpeg"));
    }

    /**
     * Uploads image as JPEG.
     * 
     * @param GdImage $Image Image to upload.
     * @param int $Compression Compression level.
     * @return string Name of the image file.
     * 
     * @since 1.0.0
     */
    function UploadImage(GdImage $Image, int $Compression, string $Directory)
    {
        while(true)
        {
            $Name = RandomString();
            if(!file_exists($Directory . "$Name.jpeg"))
            {
                break;
            }
        }

        imagejpeg($Image,$Directory . "$Name.jpeg", $Compression);

        return $Name;
    }

    function UploadThumbnail(GdImage $Thumbnail, $Directory)
    {
        imagejpeg($Thumbnail, $Directory, 75);
    }

    /**
     * Deletes image.
     * 
     * @param string $Name Name of the image file.
     * @return void
     * 
     * @since 1.0.0
     */
    function DeleteImage(string $Name, string $Directory)
    {
        //Delete if exist. Just to avoid annoying warnings.
        if(file_exists($Directory . "$Name.jpeg"))
        {
            unlink($Directory . "$Name.jpeg");
        }
    }
?>