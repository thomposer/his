<?php
namespace app\common;
use app\common\Common;
use Yii;
class Crop {
    private $src;
    private $data;
    private $file;
    private $dst;
    private $type;
    private $extension;
    private $baseUploadPath;//图片目录路径
    private $msg;
    private $sync_num;
    private $baseUrl;

    function __construct($src, $data, $file,$dstImgWidth ,$dstImgHeight) {
      $this->baseUrl = 'uploads/';
      $this->baseUploadPath =  $this->baseUrl.date('Y-m-d',time());
      $this->sync_num = 108;//his专用上传编号
      Common::mkdir($this->baseUploadPath);
      $this -> setSrc($src);
      $this -> setData($data);
      $this -> setFile($file);
      $this -> crop($this -> src, $this -> dst, $this -> data,$dstImgWidth,$dstImgHeight);
      
    }

    private function setSrc($src) {
      if (!empty($src)) {
        $type = exif_imagetype($src);

        if ($type) {
          $this -> src = $src;
          $this -> type = $type;
          $this -> extension = image_type_to_extension($type);
          $this -> setDst();
        }
      }
    }

    private function setData($data) {
      if (!empty($data)) {
        $this -> data = json_decode(stripslashes($data));
      }
    }

    private function setFile($file) {
      $errorCode = $file['error'];
      if ($errorCode === UPLOAD_ERR_OK) {
        $type = exif_imagetype($file['tmp_name']);

        if ($type) {
          $extension = image_type_to_extension($type);
          $src = $this->baseUploadPath . '/' . date('YmdHis') . '.original' . $extension;

          if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || strtolower($extension) == 'jpg' || $type == IMAGETYPE_PNG) {

            if (file_exists($src)) {
              unlink($src);
            }

            $result = move_uploaded_file($file['tmp_name'], $src);

            if ($result) {
              $this -> src = $src;
              $this -> type = $type;
              $this -> extension = $extension;
              $this -> setDst();
              
            } else {
//                $this -> msg = 'Failed to save file';
                  $this-> msg = '文件上传失败,请稍候再试';
            }
          } else {
//             $this -> msg = 'Please upload image with the following types: JPG, PNG, GIF';
            $this-> msg = '文件上传失败,请确认图片的格式是否正确(图片格式:JPG, PNG, GIF)';
          }
        } else {
//           $this -> msg = 'Please upload image file';
          $this -> msg = '请上传图片文件(图片格式:JPG, PNG, GIF)';
        }
      } else {
        $this -> msg = $this -> codeToMessage($errorCode);
      }
    }

    private function setDst() {
        
      $this -> dst = $this->baseUploadPath . '/' . date('YmdHis') . '.png';
    }
    public function uploadCdn(){
        return Common::syncImg(ltrim($this->dst,$this->baseUrl), $this->sync_num);//同步到cdn服务器
    }
    private function crop($src, $dst, $data,$dstImgWidth,$dstImgHeight) {
      if (!empty($src) && !empty($dst) && !empty($data)) {
        switch ($this -> type) {
          case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($src);
            break;

          case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($src);
            break;

          case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($src);
            break;
        }

        if (!$src_img) {
          $this -> msg = "Failed to read the image file";
          return;
        }

        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height


        $src_img_w = $size_w;
        $src_img_h = $size_h;

        $degrees = $data -> rotate;

        // Rotate the source image
        if (is_numeric($degrees) && $degrees != 0) {
          // PHP's degrees is opposite to CSS's degrees
          $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

          imagedestroy($src_img);
          $src_img = $new_img;

          $deg = abs($degrees) % 180;
          $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

          $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
          $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

          // Fix rotated image miss 1px issue when degrees < 0
          $src_img_w -= 1;
          $src_img_h -= 1;
        }

        $tmp_img_w = $data -> width;
        $tmp_img_h = $data -> height;
        $dst_img_w = $dstImgWidth;
        $dst_img_h = $dstImgHeight;

        $src_x = $data -> x;
        $src_y = $data -> y;

        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
          $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
          $dst_x = -$src_x;
          $src_x = 0;
          $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
          $dst_x = 0;
          $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
          $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
          $dst_y = -$src_y;
          $src_y = 0;
          $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
          $dst_y = 0;
          $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;

        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($result) {
          if (!imagepng($dst_img, $dst)) {
            $this -> msg = "Failed to save the cropped image file";
          }
        } else {
          $this -> msg = "Failed to crop the image file";
        }

        imagedestroy($src_img);
        imagedestroy($dst_img);
      }
    }

    private function codeToMessage($code) {
      switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
          $message = '文件上传失败,上传图片大小不能超过2M';
          break;

        case UPLOAD_ERR_FORM_SIZE:
//           $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
          $message = '文件上传失败,上传图片大小不能超过2M';
          break;

        case UPLOAD_ERR_PARTIAL:
//           $message = 'The uploaded file was only partially uploaded';
          $message = '文件上传失败,请确认图片的格式必须是:JPG, PNG, GIF.图片大小不能超过2M';
          break;

        case UPLOAD_ERR_NO_FILE:
//           $message = 'No file was uploaded';
          $message = '上传失败,没有文件被上传';
          break;

        case UPLOAD_ERR_NO_TMP_DIR:
//           $message = 'Missing a temporary folder';
          $message = '系统繁忙,请稍候再试';
          break;

        case UPLOAD_ERR_CANT_WRITE:
          $message = '系统繁忙,请稍候再试';
//           $message = 'Failed to write file to disk';
          break;

        case UPLOAD_ERR_EXTENSION:
//           $message = 'File upload stopped by extension';
          $message = '系统繁忙,请稍候再试';
          break;

        default:
          $message = '文件上传失败,请确认图片的格式必须是:JPG, PNG, GIF.图片大小不能超过2M';
//           $message = 'Unknown upload error';
      }

      return $message;
    }

    public function getResult() {
      $uploadUrl = !empty($this -> data) ? $this -> dst : $this -> src;
      return Yii::$app->request->baseUrl.'/'.$uploadUrl;
    }

    public function getMsg() {
      return $this -> msg;
    }
  }

//   $crop = new CropAvatar($_POST['avatar_src'], $_POST['avatar_data'], $_FILES['avatar_file']);

//   $response = array(
//     'state'  => 200,
//     'message' => $crop -> getMsg(),
//     'result' => $crop -> getResult()
//   );

//   echo json_encode($response);

