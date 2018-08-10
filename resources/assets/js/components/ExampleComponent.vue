<template>
    <div class="container motto">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header title">Our Motivation</div>

                    <div class="card-body body">
                        "Helping staff be healthier by eating and drinking better at work."
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.motto {
    margin-top: 50px;
    .card-default {
        width: 80%;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        .title {
          font-size: 20px;
          font-weight: bold;
        }
        .body {
          font-size: 35px;

        }
    }
}
</style>

<script>
    export default {
        mounted() {
            console.log('Example (aka Our Motivation) component mounted.')
        }
    }
</script>
