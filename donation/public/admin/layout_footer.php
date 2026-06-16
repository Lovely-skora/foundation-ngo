<!-- PAGE CONTENT ENDS HERE -->
  </div><!-- /content -->
</div><!-- /main -->

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}
document.addEventListener('click', function(e) {
  var sidebar = document.getElementById('sidebar');
  if (window.innerWidth <= 900 && sidebar.classList.contains('open')) {
    if (!sidebar.contains(e.target) && !e.target.closest('.mobile-menu')) {
      sidebar.classList.remove('open');
    }
  }
});
</script>
</body>
</html>
