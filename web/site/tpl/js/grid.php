<div id="container"></div>
<script>
  new Ngn.Grid({
    eParent: $('container'),
    tools: {
      special: true
    },
    data: {
      head: ['Title', 'Text', 'Image'],
      body: [
        {
          id: 1,
          rowClass: 'notRequired',
          data: [
            {
              title: 'One item',
              text: 'Large text message',
              image: '<img src="http://yastatic.net/morda-logo/i/logo.png">'
            },
            {
              title: ['One item', 'special'],
              text: 'Large text message',
              image: '<img src="http://yastatic.net/morda-logo/i/logo.png">'
            },
          ]
        }
      ]
    }
  });
</script>