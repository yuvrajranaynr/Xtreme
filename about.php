<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}



?>
<style>
    <?php include 'css/user_style.css'; ?>
</style>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HiStudy-About Page</title>

    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/user_style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<!---------------------------------- banner section------------------------------------- -->
<div class="banner">
  <div class="detail">
    <div class="title">
      <a href="index.php">home</a>
      <span> <i class="bx bx-chevron-right"></i> about </span>
    </div>
    <h1>about us</h1>
    <p>Dive in and learn React.js from scratch! Learn Reactjs, Hooks, Redux, React Routing, Animations, Next.js and way more!</p>
    <div class="flex-btn">
      <a href="login.php" class="btn">login to start</a>
      <a href="contact.php" class="btn">contact us</a>
    </div>
  </div>
  <img src="image/about.png">
</div>

<!-- ----------------------------------------about section --------------------------------------->
<div class="about">
  <div class="box-container">
    <div class="box">
      <img src="image/banner.png" class="img">
      <div class="thumbnail-1">
        <img src="image/about.jpg">
      </div>
      <div class="thumbnail-2">
        <img src="image/about1.jpg">
      </div>
      <div class="thumbnail-3">
        <img src="image/about0.jpg">
      </div>
    </div>

    <div class="box">
      <div class="title">
        <span>know about us</span>
        <h1>know about histudy learning platform</h1>
        <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
        <p>Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
      </div>

      <div class="detail">
        <i class="bx bxl-facebook"></i>
        <div>
          <h3>flexible classes</h3>
          <p>It is a long established fact that a reader will be distracted by this unreadable content when looking at its layout.</p>
        </div>
      </div>

      <div class="detail">
        <i class="bx bxl-facebook"></i>
        <div>
          <h3>learn from anywhere</h3>
          <p>It is a long established fact that a reader will be distracted by this unreadable content when looking at its layout.</p>
        </div>
      </div>

      <div class="detail">
        <i class="bx bxl-facebook"></i>
        <div>
          <h3>experience teacher's service</h3>
          <p>It is a long established fact that a reader will be distracted by this unreadable content when looking at its layout.</p>
        </div>
      </div>

      <a href="" class="btn">know more about us</a>
    </div>
  </div>
</div>

<!------------------------------------work section------------------------------------->
<div class="work">
  <div class="box-container">
    <div class="content">
      <div class="heading">
        <span>how we work</span>
        <h1>build your career and upgrade your life</h1>
        <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
        <a href="#" class="btn">know more about us</a>
      </div>
    </div>
    <div class="img-box">
      <img src="image/about2.png" alt="How We Work Image">
    </div>
  </div>
</div>
<!----------------------------------testimonial section------------------------------------->
<div class="testimonial-container">
  <div class="heading">
    <span>learners feedback</span>
    <h1>what people say about us</h1>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto dolorum deserunt minus veniam tenetur</p>
  </div>
  <div class="container">
    <div class="testimonial-item active">
      <i class="bx bxs-quote-right" id="quote"></i>
      <img src="image/client-01.png" alt="Client 01">
      <h1>John smith</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div class="testimonial-item">
      <i class="bx bxs-quote-right" id="quote"></i>
      <img src="image/client-02.png" alt="Client 01">
      <h1>sara smith</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div class="testimonial-item">
      <i class="bx bxs-quote-right" id="quote"></i>
      <img src="image/client-03.png" alt="Client 01">
      <h1>Ben John</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div class="left-arrow" onclick="prevSlide()">
        <i class="bx bx-left-arrow-alt"></i>
    </div>
    <div class="right-arrow" onclick="nextSlide()">
        <i class="bx bx-right-arrow-alt"></i>
    </div>
  </div>
</div>
<!----------------------------------team section------------------------------------->
<div class="team">
  <div class="heading">
    <span>skill teacher</span>
    <h1>whose inspiration you love</h1>
  </div>
  <div class="team">
  <div class="heading">
    <span>skill teacher</span>
    <h1>whose inspiration you love</h1>
  </div>
  <div class="box-container">
    <div class="box">
      <img src="image/team-01.jpg" alt="Anya Petrova">
      <h2>Anya Petrova</h2>
      <p>Lead Software Architect</p>
      <span>St. Petersburg, RU</span>
    </div>
    <div class="box">
      <img src="image/team-02.jpg" alt="Kenji Tanaka">
      <h2>Kenji Tanaka</h2>
      <p>Senior Data Scientist</p>
      <span>Tokyo, JP</span>
    </div>
    <div class="box">
      <img src="image/team-03.jpg" alt="Isabelle Dubois">
      <h2>Isabelle Dubois</h2>
      <p>Creative Writing Instructor</p>
      <span>Paris, FR</span>
    </div>
    <div class="box">
      <img src="image/team-04.jpg" alt="Omar Hassan">
      <h2>Omar Hassan</h2>
      <p>Financial Modeling Expert</p>
      <span>Dubai, UAE</span>
    </div>
    <div class="box">
      <img src="image/team-05.jpg" alt="Priya Sharma">
      <h2>Priya Sharma</h2>
      <p>UX/UI Design Lead</p>
      <span>Mumbai, IN</span>
    </div>
    <div class="box">
      <img src="image/team-06.jpg" alt="Ricardo Silva">
      <h2>Ricardo Silva</h2>
      <p>Artificial Intelligence Researcher</p>
      <span>Lisbon, PT</span>
    </div>
  </div>
</div>
</div>


    <?php include 'components/footer.php'; ?>
    <script type="text/javascript" src="js/user_script.js"></script>
</body>
</html>
