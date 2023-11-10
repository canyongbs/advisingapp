document.addEventListener('alpine:init', () => {
  Alpine.data('userToUserChat', () => ({
    messages: [
      {
        text: 'hi'
      },
      {
        text: 'hi'
      }
    ],
    init() {
      console.log(this.messages);
    },
  }));
});
