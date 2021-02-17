<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center"><a href="https://firebase.google.com/" target="_blank"><img src="https://www.gstatic.com/devrel-devsite/prod/v0fb4b1803f033e9961238a08d52e344eadd99129bc9fd30999fe77c5f5dcfd87/firebase/images/lockup.png" width="300"></a></p>

## Laravel x Firebase Cloud Firestore

ระบบแชท

## Config

The core Firebase JS SDK is always required and must be listed first
```sh
node app
```
<p><script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script></p>
<p><script src="https://www.gstatic.com/firebasejs/8.2.5/firebase-firestore.js"></script></p>
<p><script src="https://www.gstatic.com/firebasejs/8.2.5/firebase-storage.js"></script></p>
<script>
    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    var firebaseConfig = {
        apiKey: "API_KEY",
        authDomain: "PROJECT_ID.firebaseapp.com",
        projectId: "PROJECT_ID",
        storageBucket: "PROJECT_ID.appspot.com",
        messagingSenderId: "SENDER_ID",
        appId: "APP_ID",
        measurementId: "G-MEASUREMENT_ID"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
</script>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
