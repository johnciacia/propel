<?php 
$url = get_option('siteurl');
?>
<link rel='stylesheet' href='<?php echo $url; ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=widgets,global,wp-admin&amp;ver=1546ebd7ceb8bb132cf9f374756f2704' type='text/css' media='all' /> 

<div class="widget-liquid-left">
	<div id="widgets-left">
		<div id="available-widgets" class="widgets-holder-wrap ui-droppable">
	
				<div id="tabs">
					<ul>
						<li><a href="#about"><span>About</span></a></li>
						<li><a href="#contribute"><span>Contribute</span></a></li>
						<li><a href="#faq"><span>FAQ</span></a></li>
						<li><a href="#support"><span>Support</span></a></li>
				</ul>   
				
				<div id="about">
					WordPress is already a powerful website management system that not only allows you to manage your content, but have others interact as well. Most projects rely on several pieces of software. Perhaps you use WordPress as a development blog, Salesforce for CRM, and some other software for time tracking and project management. Propel aims to combine all of these into a single interface that is already friendly and comfortable - WordPress.<br /><br />
					
					In it's short lifetime, Propel provides many of the capabilities you'd expect to see from any standard project management solution. Currently, these capabilities include the following:<br />
					<ul>
						<li>Ability to create multiple projects</li>
						<li>Ability to create tasks</li>
						<li>Track tasks by project</li>
						<li>Assign users to a task</li>
						<li>A front-end visual interface</li>
						<li>A bug reporting system</li>
					</ul><br />
					It is highly anticipated this list will expand over time and under the direction of its users. If there is a feature you would like to see in the next version of Propel please request it below.			
				</div>
				
				<div id="contribute">
					Thank you for considering contributing to the project. Every little bit helps to keep the project alive.<br /><br />
					
					Propel is a user-driven project, and all developments and enhancements depend on users like you! Please consider contributing to the project in one or more of the ways outlined below. Contributions from users like <strong>you</strong> keep the project vibrant, alive and on the path of progress.<br /><br />
					
					What can you do to keep Propel going? Read on...<br /><br />
					<strong>Donate Money</strong><br />
					Monetary donations are always nice.<br /><br />
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="RES5HEWQTLCY4">
					<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="border: 0px">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form><br />
					<hr /><br />
					<strong>Documentation</strong><br />
					What good is software if no one knows how to use it? The user interfaces are designed to be as intuitive as possible, and the code is written to be as clear and concise as possible. However, what may be intuitive, clear, and concise to one, may not be to others. Moreover, users often find ways to use software in ways the designer never imagined. If you thought something was particularly difficult, or have done something unique, blog about it, and I will repost it here with a backlink to your site.<br /><br />
					
					You can also help by translating the project into other languages.<br /><br />
					<hr /><br />
					<strong>Development</strong><br />
					I have received a lot of great feedback and ideas from you. I would love to implement them all. However, as a full time student, and a full time employee, my time is very limited. If you are familiar with WordPress, PHP, MySQL, and/or JavaScript (jQuery in particular) and are interested in in the project, please contact me and I will give you access to my development repository. I could really use one or two more developers.<br /><br />
					
					<hr /><br />
					<strong>Marketing</strong><br />
					The best thing you could do for something you love is tell your friends about it. So blog il, dig it, tweet it, write about it on your wall, or perhaps a backlink. Anything is appreciated.
					<hr /><br /><br />
					Once again, thank you for considering a contribution. Every little bit helps.			
				
				</div>
				
				<div id="faq">
				<strong>How do I display a projects status in a page?</strong><br />
				You can use the [pl-projects] short code in any page or post to show the status of all your current projects. You can show the status of an individual project by using [pl-projects=x] where x is the id of the project.<br /><br />
				
				<strong>Can I change the way the project status looks?</strong><br />
				Yes, although this feature is currently not supported. Most of the interface uses the jQuery UI. You can go to <a href="http://jqueryui.com/themeroller/">http://jqueryui.com/themeroller</a> and design and download your own interface. Once you have downloaded and extracted the theme, upload the files in css/custom-theme/ to wp-content/plugins/propel/css/redmond/. Be sure to keep the name of the CSS file the same. If you choose to change your style, be sure to backup the any necessary files before you update the plugin as any changes you have made will be overwritten.  This feature will be fully supported in later versions.
				
				
				
				</div>
				
				<div id="support">For additional help you can email admin [at] johnciacia [dot] com or post a comment at <a href="http://www.johnciacia.com/propel/">http://www.johnciacia.com/propel/</a></div>
				
				</div>
			<br class="clear">
		</div>
	
	
	</div>
</div>

<div class="widget-liquid-right">
	<div id="widgets-right">
		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
			<div class="sidebar-name-arrow"><br></div>
			<h3>Latest News</h3></div>
			<div id="primary-widget-area" class="widgets-sortables ui-sortable" style="min-height: 50px; ">
				<div class="sidebar-description">
					<ul>
					    <?php if ($maxitems == 0) echo '<li>No items.</li>';
					    else
					    // Loop through each feed item and display each item as a hyperlink.
					    foreach ($rss_items as $item) {?>
					    
					    <li>
					        <a href='<?php echo $item->get_permalink(); ?>'
					        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
					        <?php echo $item->get_title(); ?></a><br />
					        <?php 
					        	$description = $item->get_description(); 
					        	$description = substr($description, 0, -37);
					        	echo $description;
					        ?>
					    </li>
					    <?php }; ?>
					</ul>	
				
				</div>
			</div>
		</div>
		<?php 
		/* 
		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
			<div class="sidebar-name-arrow"><br></div>
			<h3>Latest Comments</h3></div>
			<div id="primary-widget-area" class="widgets-sortables ui-sortable" style="min-height: 50px; ">
				<div class="sidebar-description">
				
					<ul>
					    <?php if ($maxitems2 == 0) echo '<li>No items.</li>';
					    else
					    // Loop through each feed item and display each item as a hyperlink.
					    foreach ($rss_items2 as $item) {?>
					    
					    <li>
					        <a href='<?php echo $item->get_permalink(); ?>'
					        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
					        <?php echo $item->get_title(); ?></a><br />
					        <?php 
					        	$description = $item->get_description(); 
					        	//$description = substr($description, 0, -37);
					        	if(strlen($description) > 100) echo substr($description, 0, 100) . "... ";
					        	else echo $description;
					        ?>
					    </li>
					    <?php }; ?>
					</ul>
				
				</div>
			</div>
		</div>
		*/
		?>
	</div>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery("#tabs").tabs();
	});
</script>