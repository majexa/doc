<a href="" class="btn"><span>Title</span></a>

<script>
  new Ngn.Btn(document.getElement('#demo .btn'), null, {
    request: new Request.JSON({
      url: '/default/requests/json_delayed'
    })
  });
</script>
