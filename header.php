<?php
include 'config/config.php';
$cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
?>
<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-white text-black dark:bg-black dark:text-white shadow sticky top-0 z-50 w-full transition-colors">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 h-16">
        <div class="flex items-center gap-4">
            <span class="flex items-center font-bold text-xl text-black dark:text-white gap-2 select-none">
                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-br from-black to-gray-800 dark:from-white dark:to-gray-400 text-white dark:text-black">📰</span>
                Routine of Raskot Banda
            </span>
            <label class="flex items-center cursor-pointer ml-2" title="Toggle light/dark mode">
                <input type="checkbox" id="themeToggle" onchange="toggleTheme()" class="hidden">
                <span class="w-11 h-6 bg-gray-300 dark:bg-gray-700 rounded-full relative transition-colors">
                    <span class="absolute left-1 top-1 w-4 h-4 bg-white dark:bg-black rounded-full transition-transform" id="themeThumb"></span>
                </span>
                <span class="ml-2 text-lg" id="themeIcon">🌙</span>
            </label>
        </div>
        <div class="hidden md:flex items-center gap-2" id="mainNavLinks">
            <a href="index.php" class="px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">Home</a>
            <?php if ($cat_result && $cat_result->num_rows > 0): ?>
                <?php while($cat = $cat_result->fetch_assoc()): ?>
                    <a href="category.php?id=<?php echo $cat['id']; ?>" class="px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <button class="md:hidden flex flex-col justify-center items-center w-10 h-10" id="mainNavHamburger" onclick="toggleNavLinks()">
            <span class="block w-7 h-1 bg-black dark:bg-white rounded mb-1"></span>
            <span class="block w-7 h-1 bg-black dark:bg-white rounded mb-1"></span>
            <span class="block w-7 h-1 bg-black dark:bg-white rounded"></span>
        </button>
    </div>
    <div class="md:hidden px-4 pb-2" id="mainNavLinksMobile" style="display:none;">
        <a href="index.php" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">Home</a>
        <?php
        $cat_sql2 = "SELECT * FROM categories ORDER BY name ASC";
        $cat_result2 = $conn->query($cat_sql2);
        if ($cat_result2 && $cat_result2->num_rows > 0):
            while($cat = $cat_result2->fetch_assoc()): ?>
                <a href="category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium"><?php echo htmlspecialchars($cat['name']); ?></a>
        <?php endwhile; endif; ?>
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