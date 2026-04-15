<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <url>
        <loc><?php echo base_url(); ?></loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>

    <?php if(!empty($categories)): foreach($categories as $category): 
        // Ưu tiên lấy cột 'slug' trong database nếu có, nếu không thì tự tạo slug từ 'name'
        if (isset($category['slug']) && !empty($category['slug'])) {
            $cat_slug = $category['slug'];
        } else {
            $cat_name = isset($category['name']) ? $category['name'] : 'category';
            $cat_slug = function_exists('slugify') ? slugify($cat_name) : url_title($cat_name, 'dash', TRUE);
        }
    ?>
    <url>
        <loc><?php echo base_url('home/courses?category='.$cat_slug); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php endforeach; endif; ?>

    <?php if(!empty($courses)): foreach($courses as $course): 
        $c_title = isset($course['title']) ? $course['title'] : 'course';
        $slug    = function_exists('slugify') ? slugify($c_title) : url_title($c_title, 'dash', TRUE);
        $lastmod = (!empty($course['last_modified'])) ? date('Y-m-d', $course['last_modified']) : date('Y-m-d');
    ?>
    <url>
        <loc><?php echo site_url('home/course/'.$slug.'/'.$course['id']); ?></loc>
        <lastmod><?php echo $lastmod; ?></lastmod>
        <priority>0.8</priority>
    </url>
    <?php endforeach; endif; ?>

    <?php if(!empty($blogs)): foreach($blogs as $blog): 
        $b_title = isset($blog['title']) ? $blog['title'] : 'blog';
        $slug    = function_exists('slugify') ? slugify($b_title) : url_title($b_title, 'dash', TRUE);
        $date    = isset($blog['added_date']) ? $blog['added_date'] : time();
        $b_id    = isset($blog['blog_id']) ? $blog['blog_id'] : (isset($blog['id']) ? $blog['id'] : '');
    ?>
    <url>
        <loc><?php echo site_url('blog/details/'.$slug.'/'.$b_id); ?></loc>
        <lastmod><?php echo date('Y-m-d', $date); ?></lastmod>
        <priority>0.7</priority>
    </url>
    <?php endforeach; endif; ?>

</urlset>