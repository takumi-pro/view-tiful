<footer id="footer" class="min" style="width:100%;">
  Copyright <a href="#" style="color:white;">view-tiful</a>. All Rights Reserved.
</footer>
<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
  $(function(){

    // フッターを最下部に固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }

    var $jsshow = $('#js-show-msg'),
        msg = $jsshow.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g,"").length){
      $jsshow.slideToggle('slow');
      setTimeout(function(){
        $jsshow.slideToggle('slow');
      },3000);
    }
    
    //ライブプレビュー
    var $inputfile = $('.input-file'),
        $file = $('.file'),
        $culom = $('.culom');
    $inputfile.on('dragover',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border:'4px dashed #ccc'
      });
    });
    $inputfile.on('dragleave',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border:'none'
      });
    });
    $file.on('change',function(e){
      $(this).closest('.input-file').css({
        border:'none',
        backgroundColor:'transparent'
      });
      var fileimg = this.files['0'],
          $img = $(this).siblings('.prev-img'),
          fileReader = new FileReader();
      
       
      fileReader.onload = function(event){
        $img.attr('src',event.target.result).show();
      };

      fileReader.readAsDataURL(fileimg);
    });

    var $like,
        likephotoId;
    $like = $('.js-like') || null;
    likephotoId = $like.data('photoid') || null;

    if(likephotoId !== undefined && likephotoId !== null){
      $like.on('click',function(){
        var $this = $(this);
        $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{photoId:likephotoId}
        }).done(function(data){
          console.log('success');
          $this.toggleClass('active');
        }).fail(function(msg){
          console.log('err');
        });
      });
    }

    //フォロー
    var $follow,
        followId;
    $follow = $('.js-follow') || null;
    followId = $follow.data('followid') || null;

    if(followId !== undefined && followId !== null){
      $follow.on('click',function(){
        var $this = $(this);
        $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{followIdkey:followId}
        }).done(function(data){
          console.log('success');
          $this.toggleClass('active');
        }).fail(function(msg){
          console.log('err');
        });
      });
    }

    var $switchImgSubs = $('.js-switch-img-sub'),
        $switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
    });
    
  });
</script>
</body>
</html>