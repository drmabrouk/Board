<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Table Implementation
 */
$programs = array();
$db_programs = \GSHB\Board\Database\Manager::get_programs();

if (!empty($db_programs)) {
    foreach ($db_programs as $p) {
        $programs[] = array(
            'title' => $p->title,
            'code'  => $p->code ?: 'N/A',
            'type'  => $p->type ?: 'Course',
            'dur'   => $p->duration ?: 'N/A',
            'desc'  => $p->description,
            'cat'   => $p->category ?: 'General',
            'inst'  => $p->instructor ?: 'N/A',
            'cred'  => $p->credits ?: 0
        );
    }
}
?>

<div class="board-container" style="padding-top: 50px;">
    <div style="text-align: center; margin-bottom: 80px;">
        <h1 style="font-size: 52px; font-weight: 800; letter-spacing: -2px; margin-bottom: 15px; line-height: 1;"><?php _e('Professional Programs', 'board'); ?></h1>
        <p style="font-size: 20px; color: #666; max-width: 800px; margin: 0 auto; line-height: 1.5;"><?php _e('Explore advanced clinical courses, accredited diplomas, and global health leadership pathways designed for industry-leading professionals.', 'board'); ?></p>
    </div>

    <div style="margin-bottom: 50px; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 25px; max-width: 1100px; margin-left: auto; margin-right: auto; background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
        <div style="position: relative;">
            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Programs', 'board'); ?></label>
            <input type="text" id="program-search" placeholder="<?php _e('Search by title, code, or instructor...', 'board'); ?>" style="width: 100%; padding: 15px; border: 1px solid var(--board-black); border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Program Type', 'board'); ?></label>
            <select id="program-type-filter" style="width: 100%; padding: 15px; border: 1px solid var(--board-black); border-radius: 6px;">
                <option value=""><?php _e('All Pathways', 'board'); ?></option>
                <option value="course"><?php _e('Course', 'board'); ?></option>
                <option value="diploma"><?php _e('Diploma', 'board'); ?></option>
                <option value="board membership"><?php _e('Board Membership', 'board'); ?></option>
                <option value="accreditation"><?php _e('Accreditation', 'board'); ?></option>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Category Filter', 'board'); ?></label>
            <select id="program-category-filter" style="width: 100%; padding: 15px; border: 1px solid var(--board-black); border-radius: 6px;">
                <option value=""><?php _e('All Categories', 'board'); ?></option>
                <?php
                $all_cats = array_unique(array_column($programs, 'cat'));
                foreach($all_cats as $cat) echo '<option value="'.strtolower($cat).'">'.esc_html($cat).'</option>';
                ?>
            </select>
        </div>
    </div>

    <div class="board-programs-grid">
        <?php foreach ($programs as $program) : ?>
            <div class="board-program-card" data-title="<?php echo strtolower($program['title']); ?>" data-type="<?php echo strtolower($program['type']); ?>" data-category="<?php echo strtolower($program['cat']); ?>">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                    <h3 style="margin: 0; font-size: 20px; line-height: 1.2;"><?php echo esc_html($program['title']); ?></h3>
                    <span class="status-badge" style="font-size: 10px; background: #f0f0f0; border-radius: 4px;"><?php echo esc_html($program['code']); ?></span>
                </div>

                <div style="margin-bottom: 20px; font-size: 13px; color: #666;">
                    <span style="display: inline-flex; align-items: center; margin-right: 15px;"><span class="dashicons dashicons-category" style="font-size: 16px; width: 16px; height: 16px; margin-right: 5px;"></span> <?php echo esc_html($program['type']); ?></span>
                    <span style="display: inline-flex; align-items: center;"><span class="dashicons dashicons-clock" style="font-size: 16px; width: 16px; height: 16px; margin-right: 5px;"></span> <?php echo esc_html($program['dur']); ?></span>
                </div>

                <div class="board-program-card-content" style="flex-grow: 1;">
                    <p style="font-size: 14px; line-height: 1.6; color: #444; margin-bottom: 25px;"><?php echo esc_html($program['desc']); ?></p>

                    <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px;">
                            <strong><?php _e('Instructor:', 'board'); ?></strong>
                            <span><?php echo esc_html($program['inst']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <strong><?php _e('Academic Credits:', 'board'); ?></strong>
                            <span><?php echo intval($program['cred']); ?> Units</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <a href="<?php echo home_url('/program/' . $program['code']); ?>" class="board-btn-black board-btn-outline" style="width: 100%; border-radius: 6px; text-transform: none; font-weight: 700;"><?php _e('Details & Apply', 'board'); ?></a>
                        <a href="<?php echo home_url('/qb?p=' . urlencode($program['code'])); ?>" class="board-btn-black" style="width: 100%; border-radius: 6px; text-transform: none; font-weight: 700;"><?php _e('Assessments', 'board'); ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
