<?php
/*
Plugin Name: Example Testimonial Plugin
Plugin URI: https://jadipesan.com
Description: Simple non-bloated WordPress Contact Form
Version: 1.0
Author: masdudung
Author URI: https://jadipesan.com
*/

function html_form_code() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>';
	echo 'Your Name (required) <br/>';
	echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Your Email (required) <br/>';
	echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Phone (required) <br/>';
	echo '<input type="text" name="cf-phone" pattern="[0-9 ]+" value="' . ( isset( $_POST["cf-phone"] ) ? esc_attr( $_POST["cf-phone"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Your Testimonial (required) <br/>';
	echo '<textarea rows="10" cols="35" name="cf-testimonial">' . ( isset( $_POST["cf-testimonial"] ) ? esc_attr( $_POST["cf-testimonial"] ) : '' ) . '</textarea>';
	echo '</p>';
	echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
	echo '</form>';
}

function saveToDatabase() {
	global $wpdb;

	// if the submit button is clicked, send the email
	if ( isset( $_POST['cf-submitted'] ) ) {

		// sanitize form values
		$data['name']    = sanitize_text_field( $_POST["cf-name"] );
		$data['email']   = sanitize_email( $_POST["cf-email"] );
		$data['phone'] = sanitize_text_field( $_POST["cf-phone"] );
		$data['testimonial'] = esc_textarea( $_POST["cf-testimonial"] );

		$wpdb->insert( 
			'testimoni', 
			array(
				'name' => $data['name'],
				'email' => $data['email'],
				'phone' => $data['phone'],
				'testimonial' => $data['testimonial'],
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
		if ( $data ) {
			echo '<div>';
			echo '<p>Thanks for contacting me, expect a response soon.</p>';
			echo '</div>';
		} else {
			echo 'An unexpected error occurred';
		}
	}
}

function cf_shortcode() {
	ob_start();
	saveToDatabase();
	html_form_code();

	return ob_get_clean();
}

add_shortcode( 'sitepoint_contact_form', 'cf_shortcode' );
add_action( 'admin_menu', 'my_testimonial_menu' );

function my_testimonial_menu() {
	add_menu_page( 'My Testimonial', 'Testimonial', 'manage_options', 'my_testimonial.php', 'my_testimonial', 'dashicons-tickets', 6  );
}

function my_testimonial(){
    global $wpdb;

	echo '<div class="wrap">';
	echo '<h2>List Of Testimonial</h2>';
    echo '</div>';
    echo "<hr>";
    

    if ( isset( $_POST['cf-deleted'] ) ) {
        $id = $_POST["cf-id"];
        $wpdb->delete( 'testimoni', array( 'id' => $id ), array( '%d' ) );
        echo "Data telah terhapus";
    }
    
    $testimonial = $wpdb->get_results( 
		"
		SELECT * 
		FROM testimoni
		",
		OBJECT
	);
    
    //draw table
    echo '
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">No</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Phone</th>
            <th scope="col">Testimonial</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
    ';

    $no = 1;
    foreach ($testimonial as $key => $row) {
        # code...
        echo '<tr>';
        echo '<th scope="row">'.$no.'</th>';
        echo "<td>$row->name</td>";
        echo "<td>$row->email</td>";
        echo "<td>$row->phone</td>";
        echo "<td>$row->testimonial</td>";
        echo '<td>
            <form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
            <input type="hidden" name="cf-id" value="'.$row->id.'">
            <input type="submit" class="btn btn-danger" name="cf-deleted">
            </form>
        <td>';
        echo '</tr>';
      $no++;
        
    }

    echo '
        </tbody>
        </table>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    ';

}


function masdudung_register_widget() {
register_widget( 'masdudung_widget' );
}

add_action( 'widgets_init', 'masdudung_register_widget' );

class masdudung_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // ID widget
        'masdudung_widget',
        // nama widget
        __('Contoh Widget Masdudung', ' masdudung_widget_testimoni'),
        // deskripsi widget
        array( 'description' => __( 'Coba widget masdudung', 'masdudung_widget_testimoni' ), )
        );
    }

    public function get_random_testimoni(){
        global $wpdb;

        $testimoni = $wpdb->get_results( 
            "
            SELECT * FROM testimoni
            ORDER BY RAND()
            LIMIT 1;
            ",
            OBJECT
        );
        return $testimoni;
    }
    

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $args['before_widget'];
        //if title is present
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];
        
        //output
        $testimoni = $this->get_random_testimoni();
        var_dump($testimoni[0]);

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
        $title = $instance[ 'title' ];
        else
        $title = __( 'Masdudung', 'masdudung_widget_testimoni' );
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }

}
?>