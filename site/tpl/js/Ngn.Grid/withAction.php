<div id="table"></div>
<script>
  new Ngn.Grid({
    toolActions: {
      edit: function(row) {
        alert(row.id);
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