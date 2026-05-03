<?php
/**
 * Pagination Helper
 * Renders Bootstrap Minty-themed pagination controls
 */

function get_pagination_info($total_records, $records_per_page = 10) {
    $total_pages = ceil($total_records / $records_per_page);
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $current_page = min($current_page, $total_pages); // Ensure current page doesn't exceed total pages
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_records' => $total_records,
        'records_per_page' => $records_per_page,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset
    ];
}

function render_pagination($pagination, $query_params = '') {
    if ($pagination['total_pages'] <= 1) {
        return; // No pagination needed
    }
    
    $current_page = $pagination['current_page'];
    $total_pages = $pagination['total_pages'];
    
    // Build query string
    $separator = (strpos($query_params, '?') !== false) ? '&' : '?';
    $base_url = $_SERVER['REQUEST_URI'];
    if (strpos($base_url, '?') !== false) {
        $base_url = substr($base_url, 0, strpos($base_url, '?'));
    }
    
    // Remove page parameter if exists
    $params = [];
    foreach ($_GET as $key => $value) {
        if ($key !== 'page') {
            $params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    
    $base_url .= (count($params) > 0) ? '?' . implode('&', $params) : '';
    $separator = (count($params) > 0) ? '&' : '?';
    
    ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center" style="margin-bottom: 2rem;">
            <!-- Previous Button -->
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo ($current_page > 1) ? $base_url . $separator . 'page=' . ($current_page - 1) : '#'; ?>" style="border-radius: 6px 0 0 6px; color: #078282; border-color: #dee2e6;">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            </li>
            
            <!-- Page Numbers -->
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($start_page > 1) {
                ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url . $separator . 'page=1'; ?>" style="color: #078282; border-color: #dee2e6;">1</a>
                </li>
                <?php if ($start_page > 2) { ?>
                    <li class="page-item disabled">
                        <span class="page-link" style="border-color: #dee2e6;">...</span>
                    </li>
                <?php } ?>
            <?php } ?>
            
            <?php
            for ($page = $start_page; $page <= $end_page; $page++) {
                if ($page == $current_page) {
                    ?>
                    <li class="page-item active">
                        <span class="page-link" style="background-color: #078282; border-color: #078282;"><?php echo $page; ?></span>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $base_url . $separator . 'page=' . $page; ?>" style="color: #078282; border-color: #dee2e6;"><?php echo $page; ?></a>
                    </li>
                    <?php
                }
            }
            ?>
            
            <?php
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    ?>
                    <li class="page-item disabled">
                        <span class="page-link" style="border-color: #dee2e6;">...</span>
                    </li>
                    <?php
                }
                ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url . $separator . 'page=' . $total_pages; ?>" style="color: #078282; border-color: #dee2e6;"><?php echo $total_pages; ?></a>
                </li>
                <?php
            }
            ?>
            
            <!-- Next Button -->
            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo ($current_page < $total_pages) ? $base_url . $separator . 'page=' . ($current_page + 1) : '#'; ?>" style="border-radius: 0 6px 6px 0; color: #078282; border-color: #dee2e6;">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    
    <div style="text-align: center; margin-bottom: 2rem;">
        <small style="color: #6c757d; font-weight: 500;">
            Page <span style="color: #078282; font-weight: 700;"><?php echo $current_page; ?></span> of <span style="color: #078282; font-weight: 700;"><?php echo $total_pages; ?></span>
        </small>
    </div>
    <?php
}
?>
