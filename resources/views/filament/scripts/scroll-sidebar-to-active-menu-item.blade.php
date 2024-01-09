<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const activeSidebarItem = document.querySelector('.fi-sidebar-item-active');
            const sidebarWrapper = document.querySelector('.fi-sidebar-nav');

            sidebarWrapper.scrollTo(0, activeSidebarItem.offsetTop - (window.innerHeight / 2));
        }, 0);
    })
</script>
