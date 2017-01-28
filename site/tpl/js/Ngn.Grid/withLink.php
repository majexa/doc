<div id="table"></div>
<script>
  new Ngn.Grid({
    toolLinks: {
      edit: function(row) {
        return '/some/path/' + row.id;
      }
    },
    data: {
      head: ['ID', 'Title'],
      body: [{
        id: 1,
        tools: {
          edit: 'Редактировать'
        },
        data: {
          id: 1,
          title: 'Some text'
        }
      }]
    }
  });
</script>