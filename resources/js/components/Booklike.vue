<template>
    <span v-if="!liked" v-on:click="like(bookId)"><i class="far fa-heart"></i> {{ likeCount }}</span>
    <span v-else v-on:click="unlike(bookId)"><i class="fas fa-heart"></i> {{ likeCount }}</span>
</template>

<script>
    export default {
        props: ['userId', 'bookId', 'defaultLiked', 'defaultCount'],
        // created: {

        // },
        data: function() {
            return {
                liked: false,
                likeCount: 0,
            };
        },

        created: function() {
            this.liked = this.defaultLiked
            this.likeCount = this.defaultCount
        },

        methods: {
            like: function() {
                if (this.userId == null) {
                    return window.location.href='/login'
                }
                let self = this
                let url = `/api/booklikes/like`
                axios.post(url, {
                    user_id: this.userId,
                    book_id: this.bookId
                })
                .then(function (response) {
                    // handle success
                    self.liked = true
                    self.likeCount = response.data.likeCount
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    alert(error)
                    // console.log(error);
                })
                .finally(function () {
                    // always executed
                });
            },

            unlike: function() {
                if (this.userId == null) {
                    return window.location.href='/login'
                }
                let self = this
                let url = `/api/booklikes/unlike`
                axios.post(url, {
                    user_id: this.userId,
                    book_id: this.bookId
                })
                .then(function (response) {
                    // handle success
                    self.liked = false
                    self.likeCount = response.data.likeCount
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    alert(error)
                    // console.log(error);
                })
                .finally(function () {
                    // always executed
                });
            }
        },
    }
</script>

<style>
    .fa-heart {
        color: #FF0066;
    }
</style>
