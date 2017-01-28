<script>
  new Ngn.Dialog.ImageSelect({
    title: '123',
    images: [
      {
        url: '/i/img/banners/cloud.png',
        id: 1
      }
    ],
    onOkClose: function () {
      console.debug(this.selectedImageId);
    }
  });
</script>