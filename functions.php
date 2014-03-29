<?php
add_theme_support('post-thumbnails');

require_once(get_template_directory().'/admin/framework.php');
require_once(get_template_directory().'/admin/config/config.php');

function genelia_header_menu() {
  register_nav_menu('header-menu',__( 'Genelia Header Menu' ));
}
add_action( 'init', 'genelia_header_menu' );
add_action('admin_print_footer_scripts','video_buttons');
function video_buttons() {
?>
<script type="text/javascript" charset="utf-8">
QTags.addButton( 'youtube', 'Youtube', '[video_youtube]', '[/video_youtube]', 'Youtube' );
</script>
<?php
}
function parse_youtube_url($url){
 $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}
function video_source( $param , $content=null) {
	$content = parse_youtube_url($content);
    return "<p><iframe frameborder='0' allowfullscreen src='http://www.youtube.com/embed/{$content}' width='600' height='365'></iframe></p>";
}
add_shortcode('video_youtube', 'video_source');
function pagination_run(){
global $wp_query;
$big = 999999999;
echo paginate_links( array( 'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                            'format'  => '?paged=%#%',
                            'current' => max( 1, get_query_var( 'paged' ) ),
                            'total'   => $wp_query->max_num_pages,
                            'end_size'=> 1,
                            'mid_size'=> 5 ,
							'prev_next'    => true,
							'prev_text'    => __('<<'),
							'next_text'    => __('>>')
));
}
function getFirstCat(){
$category = get_the_category();
if($category){
echo $category[0]->cat_name;
}
else{
echo "Kategori Yok";
}
}
function cat_run(){
$categories = get_categories();
if($categories){
foreach($categories as $category) {
?><li><a href="<?php echo get_category_link( $category->term_id );?>"><?php echo trim($category->cat_name);?></a></li><?php
}
}
}
function GetPages(){
$pageArgs = array('number'=>4,'post_type'=>'page','post_status'=>'publish','title_li'=>__('')); 
wp_list_pages($pageArgs);
}

function related_posts() {
global $post;
$categories = get_the_category($post->ID);
if($categories){
$cat_id = $categories[0]->term_id;
}
$args = array(
'posts_per_page' => 5,
'category__in'=>$cat_id
); 
$the_query = new WP_Query( $args ); 
?>

<section id="related_posts">
<?php while ( $the_query->have_posts() ) : $the_query->the_post();?>
</section>

<section class="related_item item">
<?php if (has_post_thumbnail()){ ?> 
<section class="related_thumb">
<a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail( 'related-post' ); ?></a>
</section>
<?php }else{?>
<section class="related_thumb">
<a href="<?php the_permalink(); ?>"><img src="<?php echo themeBase()."/images/no-image.png"?>" alt="related"/></a>
</section>
<?php }?>
<?php
endwhile; 
echo '<div class="clearfix"></div>'; 
wp_reset_postdata(); 
}

function getMasterTemplate($var){

switch($var){
case "header":
global $optVar;
?>
	<header class="clearfix" id="header">
		<div class="pull-left">
			<div class="logo">
					<?php if(!empty($optVar['logoExt'])){?>
						<a class="logo_text" data-placement="right" title="<?php bloginfo("title");?>" href="<?php bloginfo("url");?>">
							<img alt="<?php get_site_title();?>" width="151" height="56" src="<?php echo $optVar['logoExt'];?>"/>
						</a>
					<?php }else if(!empty($optVar['media']['url'])){?>
						<a href="<?php bloginfo("url");?>" data-placement="right" title="<?php bloginfo("title");?>">
							<img alt="<?php get_site_title();?>" width="151" height="56" src="<?php echo $optVar['media']['url'];?>"/>
						</a>
					<?php }else{?>
					<a class="logo_text" data-placement="right" title="<?php bloginfo("title");?>" href="<?php bloginfo("url");?>">
						<?php bloginfo("name");?>
					</a>			
					<?php }?>
			</div>
		</div>
		<div class="pull-right">
			<div class="ad728-90">
					<div class="header-ads pull-right hidden-xs">
					<?php if($optVar['header-ad']==""){?>
							<a href="#" title="Ad" data-placement="left"><img class="img-responsive" src="<?php echo themeBase();?>/images/ad728-90.png" alt="Ad728-90" /></a>
					<?php }else{echo $optVar['header-ad'];}?>
					</div>
			</div>
		</div>
	</header>
<?php
break;
case "topbar":
global $optVar;
?>
	<div id="topbar" <?php if(@$optVar["topbar-isFixed"]==1){?>data-spy="affix" data-offset-top="60" data-offset-bottom="200"<?php }?>>
		<div class="container">
			<div class="pull-left">
				<ul class="navigation clearfix hidden-xs">
					<?php if($optVar["homeMenuActive"]==1){?><li><a title="Anasayfa" data-placement="bottom" href="<?php bloginfo("url");?>"><p class="home-icon"></p></a></li><?php }?>
					<?php if($optVar["catMenuActive"]==1){?>
					<li class="dropdown">
					<a data-toggle="dropdown" href="#"><h2 class="topbar-headings">Kategoriler</h2></a>
						<ul class="dropdown-menu"><?php cat_run();?></ul>
					</li>
					<?php }?>
					<?php if($optVar["pageMenuActive"]==1){?>
					<li class="dropdown">
						<a data-toggle="dropdown" href="#"><h2 class="topbar-headings">Sayfalar</h2></a>
						<ul class="dropdown-menu">
							<?php GetPages();?>
						</ul>
					</li>
					<?php }?>
					<?php 
					$navmenu_args = array(
					'container' => '',
					'menu'=>'genelia_header_menu',
					'items_wrap' => '%3$s'
					);
					wp_nav_menu($navmenu_args);
					?>
				</ul>
			</div>
			<div class="pull-right">
			<?php if(is_user_logged_in()){?>
					<div class="pull-left user-info">Hoşgeldin,<?php global $current_user;get_currentuserinfo();echo $current_user->user_login;?></div>
			<?php }else{?>
				<div class="login-button"><a href="<?php echo wp_login_url(home_url());?>">Giriş</a></div>
				<div class="register-button"><a href="<?php echo wp_registration_url();?>">Kayıt Ol</a></div>
			<?php }?>
				<div class="search-button hidden-xs"><a href="#"><p class="search-icon"></p></a>
					<div class="search-form">
						<form role="search" method="get" action="<?php echo home_url('/');?>">
							<input type="text" class="hide" id="search_input" name="s" placeholder="Arama Yap"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
break;
case "search":
global $query_string,$wp_query;
get_header();
query_posts($query_string.'&post_type=post');
templateBase();
break;
case "cat":
global $paged;
get_header();
$cat_name = get_query_var('category_name');
query_posts(array('paged'=>$paged,'category_name' => $cat_name));
templateBase();
break;
case "page":
get_header();
global $query_string;
query_posts($query_string.'&post_type=page');
getMasterTemplate("single");
break;
case "404":
get_header();
?>
	<div class="container">
		<div class="main-part">
			<?php getMasterTemplate("header");?>
			<div id="content" class="clearfix">
				<div class="col-md-8">
					<div class="clearfix">
						<div class="post-single clearfix">
							<div class="post-single-info">
								<div class="clearfix post-single-title">
									<h1>Sayfa Bulunamadı</h1>
								</div>				
								<div class="post-single-content clearfix">
									<p>Aradığınız İçerik Bulunamadı</p>
								</div>
							</div>
						</div>
					</div>
				</div>			
				<?php get_sidebar();?>
			</div>
			<?php get_footer();?>
		</div>
	</div>
<?php 
break;
case "single":
?>
<?php get_header();
global $wp_query,$optVar;
$custom_query = $wp_query;
$post_id = $wp_query->post->ID;
$paged = (get_query_var('page')) ? get_query_var('page') : 1;
query_posts('p='.$post_id.'&post_type=post&page='.$paged);
$wp_query = $custom_query;
?>
	<div class="container">
		<div class="main-part">
			<?php getMasterTemplate("header");?>
			<div id="content" class="clearfix">
				<div class="col-md-8">
					<div class="clearfix">
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post();?>
						<div id="breadcrumb" class="clearfix">
							<div class="pull-left"><?php the_breadcrumb();?></div>
							<div class="pull-right the_author"><?php the_author();?></div>
						</div>
						<div class="post-single clearfix">
							<div class="clearfix single">
							<?php if ( has_post_thumbnail() ) {the_post_thumbnail();}?>
							</div>
							<div class="post-single-info">
								<div class="clearfix post-single-title">
									<h1><?php the_title();?></h1>
								</div>				
								<div class="post-single-content clearfix">
									<?php the_content();?>
								</div>
							</div>
						</div>	
						<?php endwhile; else: ?>
						<p><?php _e('Henüz hiç mesajını yok'); ?></p>
						<?php endif;wp_reset_query();?>
					</div>
					<?php if($optVar["sharePostActive"]==1){?>
					<div class="clearfix">
						<div class="comment-title clearfix">
							<h2>Yazıyı Paylaş</h2>
						</div>
						<div class="clearfix">
							<!-- AddToAny BEGIN -->
							<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
							<a class="a2a_dd" href="http://www.addtoany.com/share_save"></a>
							<a class="a2a_button_facebook"></a>
							<a class="a2a_button_twitter"></a>
							<a class="a2a_button_google_plus"></a>
							<a class="a2a_button_pinterest"></a>
							</div>
							<script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>
							<!-- AddToAny END -->
						</div>
					</div>
					<?php }?>
					<?php if($optVar["relatedPostActive"]==1){?>
					<div class="clearfix">
						<div class="comment-title clearfix">
							<h2>Benzer Yazılar</h2>
						</div>
						<div class="clearfix">
							<?php related_posts();?>
						</div>
					</div>
					<?php }?>
					<div class="comment-split clearfix">
						<div class="comment-title clearfix">
							<h2>Yorumlar</h2>
						</div>
						<div class="clearfix">
							<?php comments_template();?>
						</div>
					</div>
				</div>			
				<?php get_sidebar();?>
			</div>
			<?php get_footer();?>
		</div>
	</div>
<?php
break;
case "sidebar":
?>
	<aside class="col-md-4">		
		<?php dynamic_sidebar_1();?>
	</aside>
<?php
break;
case "index":
?>
<?php get_header();
templateBase();
break;
case "footer":
global $optVar;
?>
	<!--Footer Part-->
	<footer class="clearfix" id="footer-part">
		<section class='col-md-4 col-sm-4 clearfix'>
			<div class='footer-title'>
				<span>Copyright</span>
			</div>
			<div class='footer-content clearfix'>
			<!-- Please do not delete / Lütfen Silmeyiniz-->
				<p>Design:<a href="http://www.webziga.com" title="Designer" data-placement="right">Ahmet Bilal ÇELİK</a></p>
				<p>Theme:<a href="http://www.kilitbilgi.com" title="Developer" data-placement="right">kilitbilgi</a></p>
			<!-- Please do not delete / Lütfen Silmeyiniz-->
			</div>
		</section>
		<?php dynamic_sidebar_2();?>
	</footer>
	</div>
	</div>
	<!--/Footer Part-->
	<!--Bootstrap and jQuery Libraries CDN-->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="<?php echo themeBase();?>/js/script.js"></script>
<?php $ensJsCode = trim($optVar['js-code']);if($ensJsCode!=""){?><script><?php echo $ensJsCode;?></script><?php }?>
<?php 
$google_analtyics_id = trim($optVar['google_analytics_id']);
if($google_analtyics_id!=""){
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_analtyics_id;?>', '<?php bloginfo("url");?>');
  ga('send', 'pageview');

</script>
<?php }?>
<!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
        <![endif]-->
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]--><?php wp_footer();?>
<?php
break;
case "comment-page":
?>
<div class="comments-bg"><div class="comments-inner">

<?php
$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );
$fields =  array(
'fields' => apply_filters( 'comment_form_default_fields', array(
    'author' => '<p class="comment-form-padding col-md-4 col-sm-4 comment-form-author"><input class="form-control" placeholder="İsminiz" id="author" name="author" type="text" size="30"' . $aria_req . ' /></p>',
    'email'  => '<p class="comment-form-padding col-md-4 col-sm-4 comment-form-email"><input class="form-control"  placeholder="E-Posta" id="email" name="email" type="text" size="30"' . $aria_req . ' /></p>',
    'url'  => '<p class="comment-form-padding col-md-4 col-sm-4 comment-form-url"><input class="form-control"  placeholder="Website Adresiniz" id="url" name="url" type="text" size="30"' . $aria_req . ' /></p>',
	)
	),
	'comment_field'=>'<p class="comment-form-comment col-md-12 form-group" style="padding:0"><textarea class="form-control" placeholder="Yorumunuzu Yazınız..." name="comment" cols="45" rows="5" aria-required="true"></textarea></p>',
    'comment_notes_after' => '',
    'comment_notes_before' => '',
	'title_reply'=>'',
	'logged_in_as'=>'',
	'label_submit'      => __( 'Yorumu Gönder' ),
);
?>
<div id="comments"><?php if ( post_password_required() ) : ?><?php _e( 'Bu konu parola korumalıdır , yorumları görüntülemek için parolayı giriniz.', 'genelia' );?></div><?php return;endif;?><?php $pagename = get_query_var( 'cpage' );if (have_comments()) : ?><?php wp_list_comments( array('avatar_size' => '70','page'=> $pagename,'callback'=>'getComment','style'=>'div') );?><?php endif;?>
<?php if(paginate_comments_links(array('echo'=>false))!=""){?>
<div class="paginate-comments"><?php paginate_comments_links(array('prev_text'=> __('<'),'next_text'=> __('>')));?></div>
<?php }?>
</div>
<?php comment_form($fields);?>
</div>
</div>
<?php
break;
case "beginpart":
global $optVar;
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo('charset');?>"/>
<title><?php get_site_title();?></title>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<?php 
$isAutoMeta = $optVar["autoMeta"];
if($isAutoMeta==1){?>
<meta name="description" content="<?php get_site_desc();?>" />
<meta name="keywords" content="<?php get_site_keywords();?>"/>
<?php }?>
<?php wp_head();?>
<!-- Yeah , we are human -->
<link rel="author" type="text/plain" href="<?php echo themebase();?>/humans.txt" />
<!-- Bootstrap CSS CDN-->
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"/>
<!-- Wordpress Core Skin CSS-->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo themebase();?>/style.css"/>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<?php
$customFont = $optVar["custom-font"];
$customFontSelector = $optVar["custom-font-selector"];
$customFontSelector = trim($customFontSelector);
if(!empty($customFont["font-family"]) && $customFontSelector!=""){
$fontFamily = $customFont["font-family"];

$isGoogle   = $customFont["google"];
$fontWeight = $customFont["font-weight"];
$fontStyle  = $customFont["font-style"];
$subsets    = $customFont["subsets"];
$fontSize   = $customFont["font-size"];
$lineHeight = $customFont["line-height"];
$fontColor  = $customFont["color"];

if($isGoogle=="true"){
?>
<link href="http://fonts.googleapis.com/css?family=<?php echo $fontFamily.":".$fontWeight;?>&subset=<?php echo $optVar["custom-font"]["subsets"];?>" rel="stylesheet" type="text/css"/>
<?php }?>
<style type="text/css"><?php echo $customFontSelector;?>{font-family:<?php echo $fontFamily;?>!important;<?php if($fontWeight){?>font-weight:<?php echo $fontWeight;?>!important;<?php }?><?php if($fontStyle){?>font-style:<?php echo $fontStyle;?>!important;<?php }?><?php if($fontSize){?>font-size:<?php echo $fontSize;?>!important;<?php }?><?php if($lineHeight){?>line-height:<?php echo $lineHeight;?>!important;<?php }?><?php if($fontColor){?>color:<?php echo $fontColor;?>!important;<?php }?>}</style>
<?php }if(!empty($optVar['favicon-url']['url'])){?>
<link rel="icon" type="image/png" href="<?php global $optVar;echo $optVar['favicon-url']['url'];?>"/>
<!--[if IE]><link rel="shortcut icon" href="<?php echo $optVar['favicon-url']['url'];?>"/><![endif]-->
<?php }
$ensCssCode = trim($optVar['css-code']);
if($ensCssCode!=""){?>
<style type="text/css" rel="stylesheet"><?php echo $ensCssCode;?></style>
<?php }?>
<link rel="pingback" href="<?php
bloginfo('pingback_url'); ?>"/>
<?php if (is_singular() && get_option('thread_comments')) wp_enqueue_script('comment-reply');?>
</head>
<body <?php body_class(); ?>>
<?php
break;
}
}

function filteredExcerpt(){
$myExcerpt = get_the_excerpt();
$myExcerpt = substr($myExcerpt,0,250);
$tags = array("<p>", "</p>");
$myExcerpt = str_replace($tags, "", $myExcerpt);
return $myExcerpt;
}

function the_breadcrumb() {
	if (!is_home()) {
		?>
		<span class="home-text"><a href="<?php bloginfo("url");?>">Anasayfa</a> ></span>
		<span class="home-text">
		<?php
			if (is_category() || is_single()) {
			global $post;
			$categories = get_the_category( $post->ID );
			if($categories)
			$cat_id = $categories[0]->term_id;
		?>
		<a href="<?php echo get_category_link($cat_id);?>">
		<?php getFirstCat();?>
		</a>
			</span>
			<span class="home-text">
			<?php
			if (is_single()) {
				echo " > ";
			?>
			</span>
			<span class="title-text">
			<a href="<?php the_permalink();?>">
			<?php
				echo the_title();
			}
			?>
			</a>
			</span>
			<?php
		} elseif (is_page()) {
		?>
			<span class="title-text">
			<?php 
			echo the_title();
			?>
			</span>	
			<?php
		}
	}
}

function new_excerpt_more( $more ) {
	return ' <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">[...]</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );

function templateBase(){
global $wp_query,$query_string,$optVar;
$isSliderActive  = $optVar["slider-isActive"];
?>
	<div class="container">
		<div class="main-part">
			<?php getMasterTemplate("header");?>
			<div id="content" class="clearfix">
				<div class="col-md-8">
				<?php $isSliderActive  = $optVar["slider-isActive"];
				if($isSliderActive){?>
					<div id="carousel-run-it" class="carousel slide" data-ride="carousel">
						<!-- Indicators -->
						<?php $postLimitSlider = 10;$i=0;?>
						<?php if($optVar["slider-isAuto"]==1){?>
						<ol class="carousel-bullets carousel-indicators">
							<?php while($i!=$postLimitSlider){?>
							<li data-target="#carousel-run-it" data-slide-to="<?php echo $i;?>"></li>
							<?php $i++;}?>
						</ol>

						<!-- Wrapper for slides -->
						<div class="carousel-inner">
						<?php
						$args = array('showposts' => $postLimitSlider);query_posts($args);
						if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
							<div class="item">
							<?php if ( has_post_thumbnail() ) {the_post_thumbnail();}
							else{?>
							<div class="no-slide"></div>
							<?php }?>
							  <div class="carousel-title-caption">
								<a href="<?php the_permalink();?>"><?php the_title();?></a>
							  </div>
							</div>
						<?php endwhile; else: ?>
						<p><?php _e('Henüz hiç mesajınız yok'); ?></p>
						<?php endif;wp_reset_query();?>	
						</div>

						<!-- Controls -->
						<a class="carousel-nav-left" href="#carousel-run-it" data-slide="prev">
							<p class="control-icon-left"></p>
						</a>
						<a class="carousel-nav-right" href="#carousel-run-it" data-slide="next">
							<p class="control-icon-right"></p>
						</a>
						<?php }
						else{
						$sliderData = $optVar["slider-slides"];
						?>
						<ol class="carousel-bullets carousel-indicators">
							<?php while($i!=$postLimitSlider){?>
							<li data-target="#carousel-run-it" data-slide-to="<?php echo $i;?>"></li>
							<?php $i++;}?>
						</ol>

						<!-- Wrapper for slides -->
						<div class="carousel-inner">
						<?php
						$i=0;
						foreach($sliderData as $getData){
						if($postLimitSlider==$i){break;}
						?>
							<div class="item">
							<?php if($getData["image"]!=""){
							?>
							<img src="<?php echo $getData["image"];?>" alt="<?php echo $getData["title"];?>"/>
							<?php }else {?>
							<div class="no-slide"></div>
							<?php }?>
							  <div class="carousel-title-caption">
								<a class="popInfo" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="top" data-content="
								<?php $myExcerpt = $getData["description"];
								echo $myExcerpt;?>" href="<?php echo $getData["url"];?>"><?php echo $getData["title"];?></a>
							  </div>
							</div>
						<?php 
						$i++;
						}?>
						</div>

						<!-- Controls -->
						<a class="carousel-nav-left" href="#carousel-run-it" data-slide="prev">
							<p class="control-icon-left"></p>
						</a>
						<a class="carousel-nav-right" href="#carousel-run-it" data-slide="next">
							<p class="control-icon-right"></p>
						</a>
						<?php }?>
					</div>
					<?php }?>
					<div id="posts">
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post();?>
						<div class="post clearfix">
							<div class="pull-left post-image hidden-xs">
					<?php if ( has_post_thumbnail() ) {the_post_thumbnail();}
					else{?>
					<div class="no-image"></div>
					<?php }?>
								<div class="cat-hover">
									<a href="<?php the_permalink();?>"><?php getFirstCat();?></a>
								</div>
							</div>
							<div class="pull-left post-info">
								<div class="clearfix post-title pull-left">
									<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
								</div>						
								<div class="clearfix post-content pull-left">
									<?php the_excerpt();?>
								</div>
								<div class="clearfix post-details pull-left hidden-xs">
									<ul class="details-menu">
										<li><a href="<?php the_permalink();?>"><?php the_author();?></a></li>
										<li><span><?php the_time(get_option('date_format')); ?></span></li>
										<li><a href="<?php comments_link();?>"><?php comments_number( '0 Yorum', '1 Yorum', '% Yorum' ); ?></a></li>
									</ul>
								</div>
							</div>
						</div>	
						<?php endwhile; else: ?>
						<p><?php _e('Henüz hiç mesajınız yok'); ?></p>
						<?php endif;wp_reset_query();?>	
					</div>
					<div id="pagination" class="clearfix">
						<?php pagination_run();?>
					</div>
				</div>			
				<?php get_sidebar();?>
			</div>
			<?php get_footer();?>
			</body>
</html>
<?php
}

function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return 0;
    }
    return $count;
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function wpb_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar Modülü', 'wpb' ),
		'id' => 'sidebar-1',
		'description' => __( 'Bileşen desteği ile dinamik bir sidebar oluşturabilirsiniz.', 'wpb' ),
		'before_widget' => '<section class="sidebar-item clearfix">
		',
		'after_widget' => '	</div>
		</section>
		',
		'before_title' => '	<div class="sidebar-title">
				<span>',
		'after_title' => '</span>
			</div>
			<div class="sidebar-content">
	',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Modülü', 'footer-1' ),
		'id' => 'sidebar-2',
		'description' => __( 'Bileşen desteği ile dinamik bir footer oluşturabilirsiniz.', 'footer-1' ),
		'before_widget' => '
		<section class="col-md-4 col-sm-4 clearfix">',
		'after_widget' => '
		</div>
				</section>
			',
		'before_title' => '
				<div class="footer-title">
					<span>',
		'after_title' => '</span>
				</div>
				<div class="footer-content clearfix">
				',
	) );
}
add_action( 'widgets_init', 'wpb_widgets_init' );

function themeBase(){
return get_template_directory_uri();
}
function themeBaseURL(){
return get_template_directory();
}

class BlogSearch extends WP_Widget {

	function BlogSearch() {
		// Instantiate the parent object
		parent::__construct( false, 'Genelia Arama' );
	}

	function widget( $args, $instance ) {
	$title = $instance['title'];
?>
		<section class='sidebar-item clearfix'>
				<div class='sidebar-title'>
					<span><?php echo $title==""?"Arama Yap":$title;?></span>
				</div>
				<div class='sidebar-content'>
					<div class='search'>
						<form role="search" action="<?php echo home_url('/');?>" method='get'>
							<input type='text' name='s' id='s' class="form-control input-sm" placeholder="Arama Yap"/>
						</form>
					</div>
				</div>
		</section>
<?php
	}
	function form($instance) {	

	if(!$instance){
	$instance['title'] = "";
	}

    $title = esc_attr($instance['title']);
    ?>
	 <p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>	
    <?php
}

	function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
    return $instance;
	}
}

function BlogSearch_register_widgets() {
	register_widget( 'BlogSearch' );
}

add_action( 'widgets_init','BlogSearch_register_widgets');

class adsWidget extends WP_Widget {

	function adsWidget() {
		parent::__construct( false, 'Genelia Reklam Sistemi / Sidebar' );
	}
	
	function widget( $args, $instance ) {
		$title = $instance['title'];
		
		$adImage1 = $instance['adImage1'];
		$adLink1 = $instance['adLink1'];
	?>
		<section class="sidebar-item clearfix">
			<div class='sidebar-title'>
				<span><?php echo $title==""?"Reklam":$title;?></span>
			</div>
			<div class="sidebar-content ad300-250 clearfix">
				<a href='<?php echo $adLink1==""?get_bloginfo('url'):$adLink1;?>'><img width="300" height="250" src='<?php echo $adImage1==""?themeBase()."/images/ad300-250.png":$adImage1;?>' alt='Ads'/></a>
			</div>
		</section>	
	<?php 
	}
	
	function form($instance) {
	
	if(!$instance){
	$instance['title'] = "";
	$instance['adImage1']= "";
	$instance['adLink1']= "";
	}
    $title = esc_attr($instance['title']);
	
	$adImage1 = esc_attr($instance['adImage1']);
	$adLink1 = esc_attr($instance['adLink1']);
    ?>
	 <p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('adImage1'); ?>"><?php _e('Reklam 1 Resim Linki'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('adImage1'); ?>" name="<?php echo $this->get_field_name('adImage1'); ?>" type="text" value="<?php echo $adImage1; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('adLink1'); ?>"><?php _e('Reklam 1 Site Linki'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('adLink1'); ?>" name="<?php echo $this->get_field_name('adLink1'); ?>" type="text" value="<?php echo $adLink1; ?>" />
    </p>
    <?php
}

function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	
	$instance['adImage1'] = strip_tags($new_instance['adImage1']);
	$instance['adLink1'] = strip_tags($new_instance['adLink1']);
    return $instance;
}
}

function register_adsWidgets() {
	register_widget( 'adsWidget' );
}

add_action( 'widgets_init','register_adsWidgets');

class adsWidget2 extends WP_Widget {

	function adsWidget2() {
		parent::__construct( false, 'Genelia Reklam Sistemi / Footer' );
	}
	
	function widget( $args, $instance ) {
		$title = $instance['title'];
		
		$adImage1 = $instance['adImage1'];
		$adLink1 = $instance['adLink1'];
	?>
		<section class='col-md-4 col-sm-4 clearfix'>
			<div class='footer-title'>
				<span><?php echo $title==""?"Reklam":$title;?></span>
			</div>
			<div class="footer-content ad300-250 clearfix">
				<a href='<?php echo $adLink1==""?get_bloginfo('url'):$adLink1;?>'><img width="300" height="250" src='<?php echo $adImage1==""?themeBase()."/images/ad300-250.png":$adImage1;?>' alt='Ads'/></a>
			</div>
		</section>
	<?php 
	}
	
	function form($instance) {
	
	if(!$instance){
	$instance['title'] = "";
	$instance['adImage1']= "";
	$instance['adLink1']= "";
	}
    $title = esc_attr($instance['title']);
	
	$adImage1 = esc_attr($instance['adImage1']);
	$adLink1 = esc_attr($instance['adLink1']);
    ?>
	 <p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('adImage1'); ?>"><?php _e('Reklam 1 Resim Linki'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('adImage1'); ?>" name="<?php echo $this->get_field_name('adImage1'); ?>" type="text" value="<?php echo $adImage1; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('adLink1'); ?>"><?php _e('Reklam 1 Site Linki'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('adLink1'); ?>" name="<?php echo $this->get_field_name('adLink1'); ?>" type="text" value="<?php echo $adLink1; ?>" />
    </p>
    <?php
}

function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	
	$instance['adImage1'] = strip_tags($new_instance['adImage1']);
	$instance['adLink1'] = strip_tags($new_instance['adLink1']);
    return $instance;
}
}

function register_adsWidgets2() {
	register_widget( 'adsWidget2' );
}

add_action( 'widgets_init','register_adsWidgets2');

class masterSocial extends WP_Widget {

	function masterSocial() {
		parent::__construct( false, 'Genelia Sosyal Ağlar' );
	}

	function widget( $args, $instance ) {
		$title = $instance['title'];
		$socialFB = $instance['socialFB'];
		$socialTW = $instance['socialTW'];
		$socialGP = $instance['socialGP'];		
		$socialYT = $instance['socialYT'];		
		$socialFS = $instance['socialFS'];		
		$socialIG = $instance['socialIG'];		
		
		$socialFBTitle = $instance['socialFBTitle'];
		$socialTWTitle = $instance['socialTWTitle'];
		$socialGPTitle = $instance['socialGPTitle'];
		$socialYTTitle = $instance['socialYTTitle'];
		$socialFSTitle = $instance['socialFSTitle'];
		$socialIGTitle = $instance['socialIGTitle'];
?>
		<section class="sidebar-item clearfix">
			<?php if($title!=""){?>
			<div class="sidebar-title st-social clearfix">
				<span><?php echo $title;?></span>
			</div>
			<?php }?>
			<div class='social sidebar-content clearfix'>
				<ul>
					<?php if($socialFB!=""){?>
					<li><a href="<?php echo $socialFB==""?"":$socialFB;?>" data-placement="bottom" title="<?php echo $socialFBTitle==""?"Facebook":$socialFBTitle;?>" class="social-btn fb"></a></li>
					<?php }if($socialTW!=""){?>
					<li><a href="<?php echo $socialTW==""?"":$socialTW;?>" data-placement="bottom" title="<?php echo $socialTWTitle==""?"Twitter":$socialTWTitle;?>" class="social-btn tw"></a></li>
					<?php }
					if($socialGP!=""){?>
					<li><a href="<?php echo $socialGP==""?"":$socialGP;?>" data-placement="bottom" title="<?php echo $socialGPTitle==""?"Google Plus":$socialGPTitle;?>" class="social-btn gp"></a></li>
					<?php }
					if($socialYT!=""){?>
					<li><a href="<?php echo $socialYT==""?"":$socialYT;?>" data-placement="bottom" title="<?php echo $socialYTTitle==""?"Youtube":$socialYTTitle;?>" class="social-btn yt"></a></li>
					<?php }
					if($socialFS!=""){?>
					<li><a href="<?php echo $socialFS==""?"":$socialFS;?>" data-placement="bottom" title="<?php echo $socialFSTitle==""?"Foursquare":$socialFSTitle;?>" class="social-btn fs"></a></li>
					<?php }
					if($socialIG!=""){?>
					<li><a href="<?php echo $socialIG==""?"":$socialIG;?>" data-placement="bottom" title="<?php echo $socialIGTitle==""?"Instagram":$socialIGTitle;?>" class="social-btn ig"></a></li>
					<?php }?>
				</ul>
			</div>
		</section>
<?php
	}
	function form($instance) {	

	if(!$instance){
	$instance['title'] = "";
	$instance['socialFB'] = "http://www.facebook.com";
	$instance['socialTW'] = "http://www.twitter.com";
	$instance['socialGP'] = "http://plus.google.com";		
	$instance['socialYT'] = "http://www.youtube.com";		
	$instance['socialFS'] = "http://www.foursquare.com";		
	$instance['socialIG'] = "http://www.instagram.com";		
	$instance['socialFBTitle'] = "Facebook";
	$instance['socialTWTitle'] = "Twitter";
	$instance['socialGPTitle'] = "Google Plus";
	$instance['socialYTTitle'] = "Youtube";
	$instance['socialFSTitle'] = "Foursquare";
	$instance['socialIGTitle'] = "Instagram";
	}

    $title = esc_attr($instance['title']);
	$socialFB = esc_attr($instance['socialFB']);
	$socialTW = esc_attr($instance['socialTW']);
	$socialGP = esc_attr($instance['socialGP']);
	$socialYT = esc_attr($instance['socialYT']);
	$socialFS = esc_attr($instance['socialFS']);
	$socialIG = esc_attr($instance['socialIG']);
	
		
	$socialFBTitle = esc_attr($instance['socialFBTitle']);
	$socialTWTitle = esc_attr($instance['socialTWTitle']);
	$socialGPTitle = esc_attr($instance['socialGPTitle']);
	$socialYTTitle = esc_attr($instance['socialYTTitle']);
	$socialFSTitle = esc_attr($instance['socialFSTitle']);
	$socialIGTitle = esc_attr($instance['socialIGTitle']);
    ?>
	<p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>	
	<p>
      	<label for="<?php echo $this->get_field_id('socialFB'); ?>"><?php _e('Facebook Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialFB'); ?>" name="<?php echo $this->get_field_name('socialFB'); ?>" type="text" value="<?php echo $socialFB; ?>" />
    </p>		
	<p>
      	<label for="<?php echo $this->get_field_id('socialFBTitle'); ?>"><?php _e('Facebook Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialFBTitle'); ?>" name="<?php echo $this->get_field_name('socialFBTitle'); ?>" type="text" value="<?php echo $socialFBTitle; ?>" />
    </p>		
	<p>
      	<label for="<?php echo $this->get_field_id('socialTW'); ?>"><?php _e('Twitter Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialTW'); ?>" name="<?php echo $this->get_field_name('socialTW'); ?>" type="text" value="<?php echo $socialTW; ?>" />
    </p>		
	<p>
      	<label for="<?php echo $this->get_field_id('socialTWTitle'); ?>"><?php _e('Twitter Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialTWTitle'); ?>" name="<?php echo $this->get_field_name('socialTWTitle'); ?>" type="text" value="<?php echo $socialTWTitle; ?>" />
    </p>		
	<p>
      	<label for="<?php echo $this->get_field_id('socialGP'); ?>"><?php _e('Google Plus Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialGP'); ?>" name="<?php echo $this->get_field_name('socialGP'); ?>" type="text" value="<?php echo $socialGP; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('socialGPTitle'); ?>"><?php _e('Google Plus Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialGPTitle'); ?>" name="<?php echo $this->get_field_name('socialGPTitle'); ?>" type="text" value="<?php echo $socialGPTitle; ?>" />
    </p>	
	<p>
      	<label for="<?php echo $this->get_field_id('socialYT'); ?>"><?php _e('Youtube Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialYT'); ?>" name="<?php echo $this->get_field_name('socialYT'); ?>" type="text" value="<?php echo $socialYT; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('socialYTTitle'); ?>"><?php _e('Youtube Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialYTTitle'); ?>" name="<?php echo $this->get_field_name('socialYTTitle'); ?>" type="text" value="<?php echo $socialYTTitle; ?>" />
    </p>	
	<p>
      	<label for="<?php echo $this->get_field_id('socialFS'); ?>"><?php _e('Foursquare Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialFS'); ?>" name="<?php echo $this->get_field_name('socialFS'); ?>" type="text" value="<?php echo $socialFS; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('socialFSTitle'); ?>"><?php _e('Foursquare Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialFSTitle'); ?>" name="<?php echo $this->get_field_name('socialFSTitle'); ?>" type="text" value="<?php echo $socialFSTitle; ?>" />
    </p>	
	<p>
      	<label for="<?php echo $this->get_field_id('socialIG'); ?>"><?php _e('Instagram Link'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialIG'); ?>" name="<?php echo $this->get_field_name('socialIG'); ?>" type="text" value="<?php echo $socialIG; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('socialIGTitle'); ?>"><?php _e('Instagram Title'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('socialIGTitle'); ?>" name="<?php echo $this->get_field_name('socialIGTitle'); ?>" type="text" value="<?php echo $socialIGTitle; ?>" />
    </p>	
    <?php
}

	function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['socialFB'] = strip_tags($new_instance['socialFB']);
	$instance['socialTW'] = strip_tags($new_instance['socialTW']);
	$instance['socialGP'] = strip_tags($new_instance['socialGP']);
	$instance['socialYT'] = strip_tags($new_instance['socialYT']);
	$instance['socialFS'] = strip_tags($new_instance['socialFS']);
	$instance['socialIG'] = strip_tags($new_instance['socialIG']);
	$instance['socialFBTitle'] = strip_tags($new_instance['socialFBTitle']);
	$instance['socialTWTitle'] = strip_tags($new_instance['socialTWTitle']);
	$instance['socialGPTitle'] = strip_tags($new_instance['socialGPTitle']);
	$instance['socialYTTitle'] = strip_tags($new_instance['socialYTTitle']);
	$instance['socialFSTitle'] = strip_tags($new_instance['socialFSTitle']);
	$instance['socialIGTitle'] = strip_tags($new_instance['socialIGTitle']);
    return $instance;
	}
}

function socials_register_widgets() {
	register_widget( 'masterSocial' );
}

add_action( 'widgets_init','socials_register_widgets');

class categoryWidget extends WP_Widget {

	function categoryWidget() {
		parent::__construct( false, 'Genelia Kategoriler' );
	}

	function widget( $args, $instance ) {
		$title = $instance['title'];
?>
	<section class='sidebar-item clearfix'>
		<div class='sidebar-title'>
			<span><?php echo $title==""?"Kategoriler":$title;?></span>
		</div>
		<div class='sidebar-content clearfix'>
			<ul class="cat-menu">
				<?php $categories = get_categories();
				if($categories){
				foreach($categories as $category) {
				?>
				<li>
					<div class="cat-name pull-left">
						<a href="<?php echo get_category_link( $category->term_id );?>"><?php echo trim($category->cat_name);?></a>
					</div>
					<div class="cat-count pull-right">
						<a href="<?php echo get_category_link( $category->term_id );?>"><?php echo trim($category->category_count);?></a>
					</div>
				</li><?php
				}}?>
			</ul>
		</div>
	</section>
<?php }

function form($instance) {	

	if(!$instance){
	$instance['title'] = "";
	}

    $title = esc_attr($instance['title']);
    ?>
	 <p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>	
    <?php
}

function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
    return $instance;
}
}

function categoryWidget_register_widgets() {
	register_widget( 'categoryWidget' );
}

add_action( 'widgets_init','categoryWidget_register_widgets');

class popularPost extends WP_Widget {

	function popularPost() {
		parent::__construct( false, 'Genelia Popüler/Son Yazılar' );
	}

	function widget( $args, $instance ) {
	$title = $instance['title'];
	$postLimit = $instance['postLimit'];
	$listType = $instance['listType'];
?>
	<section class='sidebar-item clearfix'>
		<div class='sidebar-title'>
			<span><?php echo @$title;?></span>
		</div>
		<div class='sidebar-content clearfix'>
			<ul class="popular-post clearfix">
			<?php if($listType==2){$popular_args = array( 'showposts' => $postLimit,"order"=>"DESC",'meta_key' => 'post_views_count','orderby'=>'meta_value_num','post_type'=>'post','post_status'=>'publish');query_posts ($popular_args);}
			else{
			$popular_args = array( 'showposts' => $postLimit,"order"=>"DESC",'post_type'=>'post','post_status'=>'publish');query_posts ($popular_args);
			}
			if ( have_posts() ) : while ( have_posts() ) : the_post();?>																																				
				<li>
					<div class="pull-left popular-image">
						<?php if ( has_post_thumbnail() ) {?>
						<a href="<?php the_permalink();?>"><?php the_post_thumbnail();?></a>
						<?php }else{?>
						<a href="<?php the_permalink();?>"><img src="<?php echo themeBase();?>/images/no-image.png" alt="No-Image" /></a>
						<?php }?>
					</div>
					<div class="pull-left popular-details">
						<div class="popular-title">
							<a href="<?php the_permalink();?>"><span><?php the_title();?></span></a>
						</div>
						<div class="popular-date">
							<a href="<?php the_permalink();?>"><span><?php the_time(get_option("date_format"));?></span></a>
						</div>
					</div>
				</li>
				<?php endwhile; else: ?>
				<p><?php _e('Henüz hiç mesajını yok'); ?></p>
				<?php endif;wp_reset_query();?>
			</ul>
		</div>
	</section>
<?php 
}
function form($instance) {

	if(!$instance){
	$instance['title'] = "";
	//Varsayılan konu limiti
	$instance['postLimit']=10;
	//Varsayılan listeleme türü
	$instance['listType']=1;
	}

    $title = esc_attr($instance['title']);
    $postLimit = esc_attr($instance['postLimit']);
    $listType = esc_attr($instance['listType']);
    ?>
	 <p>
      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Başlık'); ?></label>
      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
	<p>
      	<label for="<?php echo $this->get_field_id('listType'); ?>"><?php _e('Yazı Görüntülenme Türü'); ?></label>
		<select id="<?php echo $this->get_field_id( 'listType' ); ?>" name="<?php echo $this->get_field_name( 'listType' ); ?>">
			<option value="1" <?php if($listType==1){?>selected="selected"<?php }?>>Son Yazılar</option>
			<option value="2" <?php if($listType==2){?>selected="selected"<?php }?>>Popüler Yazılar</option>
		</select>
    </p>	
	<p>
      	<label for="<?php echo $this->get_field_id('postLimit'); ?>"><?php _e('Konu Sayısı'); ?></label>
		<select id="<?php echo $this->get_field_id( 'postLimit' ); ?>" name="<?php echo $this->get_field_name( 'postLimit' ); ?>">
			<option value="1" <?php if($postLimit==1){?>selected="selected"<?php }?>>1</option>
			<option value="2" <?php if($postLimit==2){?>selected="selected"<?php }?>>2</option>
			<option value="3" <?php if($postLimit==3){?>selected="selected"<?php }?>>3</option>
			<option value="4" <?php if($postLimit==4){?>selected="selected"<?php }?>>4</option>
			<option value="5" <?php if($postLimit==5){?>selected="selected"<?php }?>>5</option>
			<option value="6" <?php if($postLimit==6){?>selected="selected"<?php }?>>6</option>
			<option value="7" <?php if($postLimit==7){?>selected="selected"<?php }?>>7</option>
			<option value="8" <?php if($postLimit==8){?>selected="selected"<?php }?>>8</option>
			<option value="9" <?php if($postLimit==9){?>selected="selected"<?php }?>>9</option>
			<option value="10" <?php if($postLimit==10){?>selected="selected"<?php }?>>10</option>
		</select>
    </p>
    <?php
}

function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['postLimit'] = strip_tags($new_instance['postLimit']);
	$instance['listType'] = strip_tags($new_instance['listType']);
    return $instance;
}
}

function popularPost_register_widgets() {
	register_widget( 'PopularPost' );
}

add_action( 'widgets_init','popularPost_register_widgets');

function dynamic_sidebar_1(){
if ( is_active_sidebar( 'sidebar-1' ) ) :
dynamic_sidebar( 'sidebar-1' );
endif;
}
function dynamic_sidebar_2(){
if ( is_active_sidebar( 'sidebar-2' ) ) :
dynamic_sidebar( 'sidebar-2' );
endif;
}

function getComment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);
		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		$comment_id = @get_comment(get_comment_ID()); 
		$author_email = $comment_id->comment_author_email;
?>
<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? 'clearfix' : 'parent') ?> id="comment-<?php comment_ID() ?>"><?php if ( 'div' != $args['style'] ) : ?><div id="div-comment-<?php comment_ID() ?>" class="pull-left clearfix comment-body"><?php endif; ?><div class="profile-image pull-left"><?php echo get_avatar( $author_email ,  65 );?></div><div class="comment-right-part"><?php printf(__('<div class="comment-author">%s</div>'), get_comment_author_link()) ?>
<div class="comment-text"><?php comment_text();?></div></div><?php if ( 'div' != $args['style'] ) : ?></div><?php endif; ?>
<?php
}
function get_site_title(){
global $page, $paged,$optVar;
wp_title('|', true, 'right');
bloginfo('name');
$site_description = get_bloginfo('description', 'display');
if ($site_description && (is_home() || is_front_page())) echo " | $site_description";
}
function get_site_desc(){
echo get_bloginfo("description");
}
function get_site_keywords(){
$title 		 = trim(wp_title('', false, 'right'));
$description = trim(get_bloginfo('description'));
$keywords = $title." ".$description;
$keywords1 = explode(" ",$keywords);
$keywords2 = implode(",",$keywords1);
echo $keywords2;
}
function scrollUp() {
  wp_register_script('scrollUp', themeBase() . '/js/scrollUp.js');
  wp_enqueue_script('scrollUp');
}
add_action('get_footer', 'scrollUp');