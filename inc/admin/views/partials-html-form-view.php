<?php







/**



 * The form to be loaded on the plugin's admin page



 */



	if( current_user_can( 'edit_users' ) ) {		



		



		// Populate the dropdown list with exising users.



        $dropdown_html = '<select required id="prwirepro_user_select" name="prwirepro[user_select]">



                            <option value="">'.__( 'Select a WP User', $this->plugin_text_domain ).'</option>';



        $wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name' ) ) );		



		



		foreach ( $wp_users as $user ) {



			$user_login = esc_html( $user->user_login );



			$user_display_name = esc_html( $user->display_name );



			



			$dropdown_html .= '<option value="' . $user_login . '">' . $user_login . ' (' . $user_display_name  . ') ' . '</option>' . "\n";



		}



        $dropdown_html .= '</select>';



		



		// Generate a custom nonce value.



		$prwirepro_add_meta_nonce = wp_create_nonce( 'prwirepro_add_user_meta_form_nonce' ); 



		



		// Build the Form



?>			


<div style="padding: 70px;">
<?php

$content = '
<b>FOR IMMEDIATE RELEASE</b>
<br><br><br>

<b>SAN FRANCISCO, DECEMBER 13, 2018</b> Grab your readers attention with an engaging first paragraph. Don’t waste any time and get directly to the point. Address each important point in this paragraph and address the who, what, where, why and how here as well.

<br>
The second paragraph should contain additional details giving context to your announcement, which can help reporters write their own story.


<br>
This section can also break up your press release highlights into a list using bullet points, which allows the reader to skim the information and get to the important details quickly.


<br>

<ul>
 	<li><b>Highlight 1</b></li>
</ul>

<br>

Additional paragraphs should contain more background information, including hard numbers to support the significance of your product or announcement. Make sure to keep any additional paragraphs short 2-4 sentences and feel free to use hyperlinks and media content such as photos, videos or audio clips.
<br>
Let your reader know what next steps you want them to take by including a call-to-action near the end of the press release. <b>If a call-to-action isn’t appropriate for your press release, then let your reader know where they can find more information. </b>


<br>
<b>About Your Company</b>
<br>
The very last paragraph of your press release is your boilerplate. This paragraph should provide a description of your business. What products or services do you provide? What is your company about? Give the reader information to help them understand the focus and purpose of your company.
</br>
Media Contact
<br>
<b>Your Name</b>
<br>
<b>Your Phone Number</b>
<br>
<b>Your Email Address</b>
<br>
<b>Your Company Website URL</b>
<br>

';
$editor_id = 'mycustomeditor';
		
$settings = array( 'editor_height' => 600 );

wp_editor( $content, $editor_id, $settings  );

?>
	
</div>
	
	

























 
	

	<br><br>




			

<br>

		



		



	<?php    



	}



	else {  



	?>



		<p> <?php __("You are not authorized to perform this operation.", $this->plugin_name) ?> </p>



	<?php   



	}



