<template>
    <div class="error-wall missing-page">
        <div class="error-container">
            <h1>oh no...</h1>
            <h3>We have had an error</h3>
            <h4>Error 404</h4>
            <p>We are sorry... the requested page doesn't exist or has been moved.</p>
            <p>
                Please
                <a href="/#/" style="color:#464444;text-decoration:none;">go to home</a>
            </p>
        </div>
    </div>
</template>

<script>
export default {};
</script>

<style lang="sass" scoped>
.error-wall
  top: 0
  left: 0
  right: 0
  bottom: 0
  width: 100%
  height: 100%
  z-index: 99999
  position: fixed
  text-align: center
  &.load-error
    background-color: #f3785e
  &.matinence
    background-color: #a473b1
  &.missing-page
    background-color: #00bbc6
  .error-container
    display: block
    width: 100%
    position: absolute
    left: 50%
    top: 50%
    transform: translate(-50%, -50%)
    -webkit-transform: translate(-50%,-50%)
    -moz-transform: translate(-50%,-50%)
    h1
      color: #fff
      font-size: 80px
      margin: 0
      @media (max-width: 850px)
        font-size: 65px
    h3
      color: #464444
      font-size: 34px
      margin: 0
      @media (max-width: 850px)
        font-size: 25px
    h4
      margin: 0
      color: #fff
      font-size: 40px
      @media (max-width: 850px)
        font-size: 35px
    p
      font-size: 15px
      &:first-of-type
        color: #464444
        font-weight: lighter
      &:nth-of-type(2)
        color: #464444
        font-weight: bold
      &.type-white
        color: #fff
      @media (max-width: 850px)
        font-size: 12px
      @media (max-width: 390px)
        font-size: 10px
</style>
