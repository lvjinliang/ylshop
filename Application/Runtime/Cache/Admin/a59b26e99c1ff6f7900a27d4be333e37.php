<?php if (!defined('THINK_PATH')) exit();?>
<?php if(is_array($data['allAttr'])): $i = 0; $__LIST__ = $data['allAttr'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i; if($attr['input_type'] == 1): ?><!-- 输入类型 手动输入-->
        <?php if(is_array($data['allAttrValue'][$attr['id']])): $k = 0; $__LIST__ = $data['allAttrValue'][$attr['id']];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrValue): $mod = ($k % 2 );++$k; if($k == 1): ?><div class="form-group filterAttr add_<?php echo ($attr['id']); ?>">
            <label class="col-sm-2 control-label filterAttr_label"><?php echo ($attr['name']); ?>：</label>
            <div class="col-sm-10 filterAttr_content">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <?php if($attr['type'] == 2): ?><!-- 属性类型 可选属性-->
                            <div class="input-group-addon" data="<?php echo ($attr['id']); ?>" onclick="addFilterAttr(this)" style="cursor:pointer;">+</div><?php endif; ?>
                            <input type="text"
                                   class="form-control"
                                   name="attr_id[<?php echo ($attr['id']); ?>][]"
                                   placeholder="<?php echo ($attr['name']); ?>"
                                   value="<?php echo ($attrValue['attr_value']); ?>" />
                        </div>
                    </div>
                    <?php if($attr['type'] == 2): ?><div class="col-sm-8">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">属性价格：</label>
                                <div class="col-sm-5">
                                    <input type="text"
                                           class="form-control"
                                           name="attr_price[<?php echo ($attr['id']); ?>][]"
                                           value="<?php echo ($attrValue['attr_price']); ?>" />
                                </div>
                            </div>
                        </div><?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="form-group filterAttr add_<?php echo ($attr['id']); ?>">
                <div class="col-sm-10 filterAttr_content  col-sm-offset-2">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <?php if($attr['type'] == 2): ?><div class="input-group-addon" data="<?php echo ($attr['id']); ?>" onclick="removeFilterAttr(this)" style="cursor:pointer;" >-</div><?php endif; ?>
                                <input type="text"
                                       class="form-control"
                                       name="attr_id[<?php echo ($attr['id']); ?>][]"
                                       placeholder="<?php echo ($attr['name']); ?>"
                                       value="<?php echo ($attrValue['attr_value']); ?>" />
                            </div>
                        </div>
                        <?php if($attr['type'] == 2): ?><div class="col-sm-8">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">属性价格：</label>
                                    <div class="col-sm-5">
                                        <input type="text"
                                               class="form-control"
                                               name="attr_price[<?php echo ($attr['id']); ?>][]"
                                               value="<?php echo ($attrValue['attr_price']); ?>" />
                                    </div>
                                </div>
                            </div><?php endif; ?>
                    </div>
                </div>
            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?><!-- 输入类型 下拉选择-->
        <?php if(is_array($data['allAttrValue'][$attr['id']])): $k = 0; $__LIST__ = $data['allAttrValue'][$attr['id']];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrValue): $mod = ($k % 2 );++$k; if($k == 1): ?><div class="form-group filterAttr add_<?php echo ($attr['id']); ?>">
                <label class="col-sm-2 control-label filterAttr_label"><?php echo ($attr['name']); ?>：</label>
                <div class="col-sm-10 filterAttr_content">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <?php if($attr['type'] == 2): ?><div class="input-group-addon" data="<?php echo ($attr['id']); ?>" onclick="addFilterAttr(this)" style="cursor:pointer;">+</div><?php endif; ?>
                                <select class="form-control" name="attr_id[<?php echo ($attr['id']); ?>][]">
                                    <?php if(is_array($attr['values'])): $i = 0; $__LIST__ = $attr['values'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option
                                            <?php if(($value) == $attrValue['attr_value']): ?>selected="true"<?php endif; ?>
                                            value="<?php echo ($value); ?>"><?php echo ($value); ?>
                                        </option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if($attr['type'] == 2): ?><div class="col-sm-8">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">属性价格：</label>
                                <div class="col-sm-5">
                                <input type="text"
                                       class="form-control"
                                       name="attr_price[<?php echo ($attr['id']); ?>][]"
                                       value="<?php echo ($attrValue['attr_price']); ?>" />
                                </div>
                            </div>
                        </div><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="form-group filterAttr add_<?php echo ($attr['id']); ?>">
                <div class="col-sm-10 filterAttr_content  col-sm-offset-2">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <?php if($attr['type'] == 2): ?><div class="input-group-addon" data="<?php echo ($attr['id']); ?>" onclick="removeFilterAttr(this)" style="cursor:pointer;" >-</div><?php endif; ?>
                                <select class="form-control" name="attr_id[<?php echo ($attr['id']); ?>][]">
                                    <?php if(is_array($attr['values'])): $i = 0; $__LIST__ = $attr['values'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option
                                        <?php if(($value) == $attrValue['attr_value']): ?>selected="true"<?php endif; ?>
                                        value="<?php echo ($value); ?>"><?php echo ($value); ?>
                                        </option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if($attr['type'] == 2): ?><div class="col-sm-8">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">属性价格：</label>
                                    <div class="col-sm-5">
                                        <input type="text"
                                               class="form-control"
                                               name="attr_price[<?php echo ($attr['id']); ?>][]"
                                               value="<?php echo ($attrValue['attr_price']); ?>" />
                                    </div>
                                </div>
                            </div><?php endif; ?>
                    </div>
                </div>
            </div><?php endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>

<script type="text/javascript" language="javascript">
function addFilterAttr(o){
    var filterHtml = $(o).parents(".filterAttr").clone();
    filterHtml.find(".filterAttr_label").remove();
    filterHtml.find(".filterAttr_content").addClass("col-sm-offset-2");
    filterHtml.find(".input-group-addon").attr("onclick",'removeFilterAttr(this)');
    filterHtml.find(".input-group-addon").html("-");
    var addAttrClass = "add_"+$(o).attr("data");
    $("."+addAttrClass).last().after(filterHtml);
}
function removeFilterAttr(o){
    $(o).parents(".filterAttr").remove();
}
</script>