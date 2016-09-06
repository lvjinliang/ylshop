<?php if (!defined('THINK_PATH')) exit();?>
<script language="javascript" type="text/javascript">
   $(function(){
       var url = "<?php echo ($url); ?>";
       var data = "param=<?php echo ($param); ?>&&pt="+$("title").html();
       $.ajax({
           url : url ,
           type : "POST",
           data : data,
           success : function(){

           }

       }) ;
   });
</script>