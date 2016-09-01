<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Template\TagLib;
use Think\Template\TagLib;
/**
 * Html标签库驱动
 */
class Html extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'editor'    => array('attr'=>'id,name,style,width,height,type','close'=>1),
        'imageUpload' => array('attr'=>'id,name,width,height,src','close'=>0)
        );

    /**
     * editor标签解析 插入可视化编辑器
     * 格式： <html:editor id="editor" name="remark" type="FCKeditor" style="" >{$vo.remark}</html:editor>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _editor($tag,$content) {
        $id			=	!empty($tag['id'])?$tag['id']: '_editor';
        $name   	=	$tag['name'];
        $style   	    =	!empty($tag['style'])?$tag['style']:'';
        $width		=	!empty($tag['width'])?$tag['width']: '100%';
        $height     =	!empty($tag['height'])?$tag['height'] :'320px';
     //   $content    =   $tag['content'];
        $type       =   $tag['type'] ;
        switch(strtoupper($type)) {
            case 'FCKEDITOR':
                $parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKeditor/fckeditor.js"></script><textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKeditor/" ; oFCKeditor.ReplaceTextarea() ;function resetEditor(){setContents("'.$id.'",document.getElementById("'.$id.'").value)}; function saveEditor(){document.getElementById("'.$id.'").value = getContents("'.$id.'");} function InsertHTML(html){ var oEditor = FCKeditorAPI.GetInstance("'.$id.'") ;if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){oEditor.InsertHtml(html) ;}else	alert( "FCK必须处于WYSIWYG模式!" ) ;}</script> <!-- 编辑器调用结束 -->';
                break;
            case 'UE':
                $parseStr   = '<textarea id="'.$id.'" name="'.$id.'" >'.$content.'</textarea>
                    <script type="text/javascript">
                        UE.getEditor("'.$id.'",
                          { initialFrameWidth: null,
                            serverUrl :"{:U("Admin/public/ueeditor")}",
                            initialFrameHeight:'.$height.'});
                    </script>';
                break;
            default :
                $parseStr  =  '<textarea id="'.$id.'" style="'.$style.'" name="'.$name.'" >'.$content.'</textarea>';
        }

        return $parseStr;
    }

    /**
     * imagUpload标签解析 上传图片标签
     * 格式： <html:imagUpload id="" name="" style="" />
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _imageUpload($tag) {
        $id			=	!empty($tag['id'])?$tag['id']: 'imgUpload';
        $name   	=	$tag['name'];
        $src    =   $tag['src'];
        $width		=	!empty($tag['width'])?$tag['width']: '100px';
        $height     =	!empty($tag['height'])?$tag['height'] :'100px';

        $parseStr  =  '<span class="uploadImg  '.$id.'">
                       <textarea style="display:none;" data="'.$id.'" id="'.$id.'"></textarea>
                       <input type="hidden" name="'.$name.'" value="'.$src.'" />
                       <img class="'.$id.'" src="'.$src.'" width="'.$width.'" height="'.$height.'"  />
                       <a href="javascript:void(0);" class="btn btn-primary add" role="button">
                       <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加
                       </a>
                       <a href="javascript:void(0);"  class="btn btn-primary remove" role="button">
                       <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>删除
                       </a>
                       </span>
                       <script type="text/javascript">
                        var upload_img_'.$id.' = UE.getEditor("'.$id.'",
                                                 { isShow:false,
                                                   serverUrl :"{:U("Admin/public/ueeditor")}",
                                                 });
                        upload_img_'.$id.'.ready(function (){
                            upload_img_'.$id.'.addListener("beforeInsertImage", function (t,arg){
                                 callBeforeInsertImage("'.$id.'", arg[0]);
                            });
                        });
                       </script>';
        return $parseStr;
    }



}