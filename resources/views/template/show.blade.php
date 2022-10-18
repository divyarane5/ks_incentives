@extends('layouts.app')

@section('content')
 <!-- Content -->

 <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Layout Demo -->
    <div class="layout-demo-wrapper">
    <div class="layout-demo-placeholder">
        <!-- <img
        src="../assets/img/layouts/layout-container-light.png"
        class="img-fluid"
        alt="Layout container"
        data-app-light-img="layouts/layout-container-light.png"
        data-app-dark-img="layouts/layout-container-dark.png"
        /> -->
       
    </div>
    <div class="layout-demo-info">
        <h4>{{$template->name}}</h4>
        <!-- <p>Container layout sets a <code>max-width</code> at each responsive breakpoint.</p> -->
    </div>
    </div>
    <!--/ Layout Demo -->
</div>
<!-- / Content -->
<?php echo $template->content; ?>
@endsection
