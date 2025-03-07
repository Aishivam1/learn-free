@extends('layouts.app', ['hideHeaderFooter' => true])

@section('title', 'Register - MentorLink')

@section('content')
<div class="register-page">
  <!-- 3D Cube Animation -->
  <div class="cube">
      <div class="face front"></div>
      <div class="face back"></div>
      <div class="face right"></div>
      <div class="face left"></div>
      <div class="face top"></div>
      <div class="face bottom"></div>
  </div>
  <div class="container">
      <h2>Create Your MentorLink Account</h2>
      <form method="POST" action="{{ route('register') }}">
          @csrf
          <input type="text" name="name" placeholder="Full Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
          <button type="submit">Register</button>
      </form>
      <div class="login-link">
          Already have an account? <a href="{{ route('login') }}">Login</a>
      </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Base styles */
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f7fa;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    perspective: 800px;
}

/* Container styling */
.register-page {
    position: relative;
}

.container {
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
    position: relative;
    z-index: 2;
}

.container h2 {
    margin-bottom: 20px;
    color: #007bff;
}

.container form {
    display: flex;
    flex-direction: column;
}

.container form input {
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.container form button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.container form button:hover {
    background-color: #0056b3;
}

.container .login-link {
    margin-top: 20px;
    font-size: 14px;
}

.container .login-link a {
    color: #007bff;
    text-decoration: none;
}

/* 3D Cube Animation */
.cube {
    width: 60px;
    height: 60px;
    position: absolute;
    top: 10%;
    right: 10%;
    transform-style: preserve-3d;
    animation: rotateCube 10s infinite linear;
    z-index: 1;
}

.cube .face {
    position: absolute;
    width: 60px;
    height: 60px;
    background: rgba(0, 123, 255, 0.7);
    border: 1px solid #fff;
}

.cube .front  { transform: translateZ(30px); }
.cube .back   { transform: rotateY(180deg) translateZ(30px); }
.cube .right  { transform: rotateY(90deg) translateZ(30px); }
.cube .left   { transform: rotateY(-90deg) translateZ(30px); }
.cube .top    { transform: rotateX(90deg) translateZ(30px); }
.cube .bottom { transform: rotateX(-90deg) translateZ(30px); }

@keyframes rotateCube {
    from { transform: rotateX(0deg) rotateY(0deg); }
    to { transform: rotateX(360deg) rotateY(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Register page loaded.');
});
</script>
@endpush
