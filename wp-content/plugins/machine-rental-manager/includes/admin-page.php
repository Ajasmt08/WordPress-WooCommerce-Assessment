<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'mrm_add_admin_menu' );

function mrm_add_admin_menu() {
    add_menu_page( 'Rental Requests', 'Rental Requests', 'manage_options', 'mrm-requests', 'mrm_admin_page', 'dashicons-clipboard', 25 );
}

function mrm_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'machine_rentals';

    // Handle Delete & Status Updates
    if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && check_admin_referer( 'mrm_admin_action' ) ) {
        $id = intval( $_GET['id'] );
        if ( $_GET['action'] == 'delete' ) {
            $wpdb->delete( $table_name, array( 'id' => $id ) );
            echo '<div class="notice notice-success"><p>Request deleted.</p></div>';
        } elseif ( in_array( $_GET['action'], array( 'Pending', 'Contacted', 'Approved', 'Rejected' ) ) ) {
            $wpdb->update( $table_name, array( 'status' => $_GET['action'] ), array( 'id' => $id ) );
            echo '<div class="notice notice-success"><p>Status updated.</p></div>';
        }
    }

    // Fetch all requests
    $requests = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
    ?>
    <div class="wrap">
        <h1>Machine Rental Requests</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Machine & Period</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $requests ) : foreach ( $requests as $req ) : 
                    $nonce_url = wp_nonce_url( admin_url( 'admin.php?page=mrm-requests&id=' . $req->id ), 'mrm_admin_action' );
                ?>
                <tr>
                    <td><?php echo esc_html( $req->id ); ?></td>
                    <td><?php echo esc_html( date( 'Y-m-d', strtotime( $req->created_at ) ) ); ?></td>
                    <td><?php echo esc_html( $req->name ); ?></td>
                    <td><?php echo esc_html( $req->email ); ?><br><?php echo esc_html( $req->phone ); ?></td>
                    <td><strong><?php echo esc_html( $req->machine_type ); ?></strong><br><?php echo esc_html( $req->rental_period ); ?></td>
                    <td><?php echo esc_html( $req->location ); ?></td>
                    <td><strong><?php echo esc_html( $req->status ); ?></strong></td>
                    <td>
                        <select onchange="if(this.value) window.location.href='<?php echo $nonce_url; ?>&action='+this.value">
                            <option value="">Update Status...</option>
                            <option value="Pending">Pending</option>
                            <option value="Contacted">Contacted</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <br><br>
                        <a href="<?php echo $nonce_url; ?>&action=delete" style="color:red;" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; else : ?>
                <tr><td colspan="8">No rental requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}