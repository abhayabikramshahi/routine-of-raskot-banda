<?php
include 'config/config.php';
$cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
?>
<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-white dark:bg-black shadow sticky top-0 z-50 w-full transition-colors border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 h-20">
        <div class="flex items-center gap-6">
            <a href="index.php" class="flex items-center gap-2 select-none">
                <span class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-700 to-blue-400 dark:from-white dark:to-gray-400 text-white dark:text-black text-2xl font-bold">📰</span>
                <span class="font-extrabold text-2xl md:text-3xl text-blue-900 dark:text-white tracking-tight">Raskot Banda</span>
            </a>
        </div>
        <div class="hidden md:flex items-center gap-4" id="mainNavLinks">
            <a href="index.php" class="px-4 py-2 rounded hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white font-semibold">Home</a>
            <div class="relative group">
                <button class="px-4 py-2 rounded hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white font-semibold flex items-center gap-1 focus:outline-none">
                    Sections
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="absolute left-0 mt-2 w-auto min-w-[400px] bg-white dark:bg-black rounded shadow-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity z-50 invisible group-hover:visible group-focus:visible flex flex-row flex-wrap gap-2 p-4">
                    <a href="diff_section.php" class="flex flex-col items-center justify-center w-32 h-24 rounded-lg bg-blue-50 dark:bg-gray-800 hover:bg-blue-100 dark:hover:bg-gray-700 text-blue-900 dark:text-white shadow transition">
                        <span class="text-2xl mb-2">🧩</span>
                        <span class="font-semibold">Diff Section</span>
                    </a>
                    <?php if ($cat_result && $cat_result->num_rows > 0): ?>
                        <?php while($cat = $cat_result->fetch_assoc()): ?>
                            <a href="category.php?id=<?php echo $cat['id']; ?>" class="flex flex-col items-center justify-center w-32 h-24 rounded-lg bg-blue-50 dark:bg-gray-800 hover:bg-blue-100 dark:hover:bg-gray-700 text-blue-900 dark:text-white shadow transition">
                                <span class="text-2xl mb-2">
                                    <?php
                                    // Simple icon mapping by category name
                                    $icon = '📁';
                                    if (stripos($cat['name'], 'tech') !== false) $icon = '💻';
                                    elseif (stripos($cat['name'], 'world') !== false) $icon = '🌍';
                                    elseif (stripos($cat['name'], 'sports') !== false) $icon = '🏅';
                                    elseif (stripos($cat['name'], 'entertain') !== false) $icon = '🎬';
                                    echo $icon;
                                    ?>
                                </span>
                                <span class="font-semibold"><?php echo htmlspecialchars($cat['name']); ?></span>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="#articles" class="ml-4 px-5 py-2 bg-blue-700 text-white rounded-lg font-semibold shadow hover:bg-blue-800 transition">Latest News</a>
        </div>
        <button class="md:hidden flex flex-col justify-center items-center w-10 h-10" id="mainNavHamburger" onclick="toggleNavLinks()">
            <span class="block w-7 h-1 bg-blue-900 dark:bg-white rounded mb-1"></span>
            <span class="block w-7 h-1 bg-blue-900 dark:bg-white rounded mb-1"></span>
            <span class="block w-7 h-1 bg-blue-900 dark:bg-white rounded"></span>
        </button>
    </div>
    <div class="md:hidden px-4 pb-2" id="mainNavLinksMobile" style="display:none;">
        <a href="index.php" class="block px-4 py-2 rounded hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white font-semibold">Home</a>
        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
        <div class="relative">
            <button onclick="toggleDropdownMobile()" class="w-full text-left px-4 py-2 rounded hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white font-semibold flex items-center gap-1 focus:outline-none">
                Sections
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="dropdownMobile" class="hidden mt-1 w-full bg-white dark:bg-black rounded shadow-lg z-50">
                <a href="diff_section.php" class="block px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white">Diff Section</a>
                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                <?php
                $cat_sql2 = "SELECT * FROM categories ORDER BY name ASC";
                $cat_result2 = $conn->query($cat_sql2);
                if ($cat_result2 && $cat_result2->num_rows > 0):
                    while($cat = $cat_result2->fetch_assoc()): ?>
                        <a href="category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-800 text-blue-900 dark:text-white"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endwhile; endif; ?>
            </div>
        </div>
        <a href="#articles" class="mt-2 block px-5 py-2 bg-blue-700 text-white rounded-lg font-semibold shadow hover:bg-blue-800 transition">Latest News</a>
    </div>
</nav>
<script>
function toggleNavLinks() {
    var links = document.getElementById('mainNavLinksMobile');
    links.style.display = links.style.display === 'none' || links.style.display === '' ? 'block' : 'none';
}
function toggleTheme() {
    var body = document.body;
    var icon = document.getElementById('themeIcon');
    var thumb = document.getElementById('themeThumb');
    var isDark = body.classList.toggle('dark');
    if (isDark) {
        icon.textContent = '☀️';
        localStorage.setItem('theme', 'dark');
        thumb.classList.add('translate-x-5');
    } else {
        icon.textContent = '🌙';
        localStorage.setItem('theme', 'light');
        thumb.classList.remove('translate-x-5');
    }
}
window.onload = function() {
    var theme = localStorage.getItem('theme');
    var body = document.body;
    var icon = document.getElementById('themeIcon');
    var toggle = document.getElementById('themeToggle');
    var thumb = document.getElementById('themeThumb');
    if (theme === 'dark') {
        body.classList.add('dark');
        icon.textContent = '☀️';
        toggle.checked = true;
        thumb.classList.add('translate-x-5');
    } else {
        body.classList.remove('dark');
        icon.textContent = '🌙';
        toggle.checked = false;
        thumb.classList.remove('translate-x-5');
    }
}
</script> 
<script>
function toggleDropdownMobile() {
    var dropdown = document.getElementById('dropdownMobile');
    dropdown.classList.toggle('hidden');
}
</script>