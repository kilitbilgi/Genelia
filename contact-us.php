<?php
/*Template Name: Genelia-Contact*/?>
<?php get_header(); ?>
	<div class="container">
		<div class="main-part">
			<?php getMasterTemplate("header");?>
			<div id="content" class="clearfix">
				<div class="col-md-8">
					<div class="clearfix">
						<div class="post-single clearfix">
							<div class="post-single-info">
								<div id="breadcrumb" class="clearfix">
									<div class="pull-left"><?php the_breadcrumb();?></div>
									<div class="pull-right the_author"><?php the_author();?></div>
								</div>
								<div class="clearfix post-single-title">
									<h1><?php the_title();?></h1>
								</div>				
								<div class="post-single-content clearfix">
															<form action="<?php echo get_bloginfo("template_directory")?>/contact-info.php" method="post">
							<div class="form-group">
								<input type="text" name="name" class="form-control" placeholder="İsim"/>
							</div>
							
							<div class="form-group">
								<input type="text" name="email" class="form-control" placeholder="E-Posta"/>
							</div>
							
							<div class="form-group">
								<select class="form-control" name="type">
									<option><span>Konu</span></option>
									<option>Genel</option>
									<option>Şikayet</option>
								</select>
							</div>
							
							<div class="form-group">
								<textarea name="message" class="form-control" placeholder="Mesajınız..."></textarea>
							</div>

							<div class="form-group">
								<input id="submit2" class="btn btn-default" name="submit" value="Gönder" type="submit"/>
							</div>
						</form>
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