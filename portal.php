<?php
session_start();
include 'config.php';

$login_err = "";

// Handle Login
if(isset($_POST['login'])){
  $uid = $conn->real_escape_string($_POST['student_id']);
  $upass = $conn->real_escape_string($_POST['password']);
  
  if($uid=="admin" && $upass=="admin123"){
    $_SESSION['user'] = "Administrator";
    $_SESSION['role'] = "admin";
    $_SESSION['db_id'] = 0;
  } else {
    $res = $conn->query("SELECT * FROM students WHERE student_id='$uid' AND password='$upass'");
    if($res->num_rows==1){
      $row = $res->fetch_assoc();
      $_SESSION['user'] = $row['name'];
      $_SESSION['role'] = "student";
      $_SESSION['db_id'] = $row['id'];
      $_SESSION['student_id'] = $row['student_id'];
    } else {
      $login_err = "Invalid ID or Password!";
    }
  }
}

// Handle Logout
if(isset($_GET['logout'])){
  session_destroy();
  header("Location: portal.php");
  exit();
}

// CRUD - Add/Edit Student (Admin only)
if(isset($_POST['save_student']) && $_SESSION['role']=='admin'){
  $name = $conn->real_escape_string($_POST['name']);
  $sid = $conn->real_escape_string($_POST['student_id_no']);
  $class = $conn->real_escape_string($_POST['class']);
  $age = (int)$_POST['age'];
  $contact = $conn->real_escape_string($_POST['contact']);
  $pass = $conn->real_escape_string($_POST['password']);
  $edit_id = (int)$_POST['edit_id'];

  if($edit_id>0){
    $conn->query("UPDATE students SET name='$name', student_id='$sid', class='$class', age=$age, contact='$contact', password='$pass' WHERE id=$edit_id");
  } else {
    $conn->query("INSERT INTO students (student_id,name,class,age,contact,password) VALUES ('$sid','$name','$class',$age,'$contact','$pass')");
  }
  header("Location: portal.php"); exit();
}

// Delete Student
if(isset($_GET['del_student']) && $_SESSION['role']=='admin'){
  $id = (int)$_GET['del_student'];
  $conn->query("DELETE FROM students WHERE id=$id");
  header("Location: portal.php"); exit();
}

// Add Grade
if(isset($_POST['save_grade']) && $_SESSION['role']=='admin'){
  $stud_id = (int)$_POST['grade_student'];
  $subj = $conn->real_escape_string($_POST['subject']);
  $score = (int)$_POST['score'];
  $term = $conn->real_escape_string($_POST['term']);
  $conn->query("INSERT INTO grades (student_db_id,subject,score,term) VALUES ($stud_id,'$subj',$score,'$term')");
  header("Location: portal.php"); exit();
}

// Delete Grade
if(isset($_GET['del_grade']) && $_SESSION['role']=='admin'){
  $id = (int)$_GET['del_grade'];
  $conn->query("DELETE FROM grades WHERE id=$id");
  header("Location: portal.php"); exit();
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal - JODASAM Academy</title><link rel="stylesheet" href="style.css">
<style>
.tabs{margin:20px 0;text-align:center}.tab-btn{padding:10px 20px;border:none;background:#ddd;border-radius:20px;margin:0 5px;cursor:pointer;font-weight:700}.tab-btn.active{background:var(--blue);color:#fff}
.action-btn{padding:6px 12px;border:none;border-radius:5px;cursor:pointer;font-size:12px;margin-right:5px;width:auto}.edit{background:#ffc107}.del{background:#dc3545;color:#fff}
.form-inline{background:#fff;padding:15px;border-radius:10px;margin:15px 0;display:flex;flex-wrap:wrap;gap:10px;box-shadow:0 3px 10px rgba(0,0,0,.05)}
</style>
</head>
<body>
<header><div class="logo">JODASAM <span>Academy</span></div>
<nav><a href="index.php">Home</a><a href="about.php">About</a><a href="contact.php">Contact</a><a href="portal.php" class="active">Portal</a></nav></header>
<div class="page-header"><h1>School Portal</h1><p>Session Authentication + CRUD Operations</p></div>

<section>
<?php if(!isset($_SESSION['user'])): ?>
<div class="form-box">
<h3 style="text-align:center;color:var(--blue)">Portal Login (PHP Session)</h3>
<?php if($login_err) echo "<p style='color:red;text-align:center'>$login_err</p>"; ?>
<form method="POST">
<input type="text" name="student_id" placeholder="Student ID e.g JODA/001" required>
<input type="password" name="password" placeholder="Password e.g 1234" required>
<button name="login" class="btn" style="width:100%">Login</button>
<p style="font-size:12px;text-align:center;margin-top:10px">Admin: admin / admin123<br>Student: JODA/001 / 1234</p>
</form>
</div>

<?php else: ?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap">
<h2 style="color:var(--blue)">Welcome <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['role']; ?>)</h2>
<a href="portal.php?logout=1" class="btn">Logout</a>
</div>

<?php if($_SESSION['role']=='admin'): ?>
<div class="tabs">
<button class="tab-btn active" onclick="showTab('students')">Students</button>
<button class="tab-btn" onclick="showTab('grades')">Grades</button>
<button class="tab-btn" onclick="showTab('messages')">Messages</button>
</div>

<!-- STUDENTS TAB -->
<div id="students-tab">
<h3>Add / Edit Student (CREATE & UPDATE)</h3>
<div class="form-inline">
<form method="POST" style="display:flex;flex-wrap:wrap;gap:10px;width:100%">
<input type="hidden" name="edit_id" id="edit_id" value="0">
<input type="text" name="name" id="sname" placeholder="Full Name" required style="flex:1;min-width:140px">
<input type="text" name="student_id_no" id="ssid" placeholder="Student ID e.g JODA/005" required style="flex:1;min-width:120px">
<input type="text" name="class" id="sclass" placeholder="Class" required style="flex:1;min-width:100px">
<input type="number" name="age" id="sage" placeholder="Age" style="flex:1;min-width:80px">
<input type="text" name="contact" id="scontact" placeholder="Parent Phone" style="flex:1;min-width:120px">
<input type="text" name="password" id="spass" placeholder="Password" value="1234" style="flex:1;min-width:100px">
<button name="save_student" class="btn" style="width:auto">Save Student</button>
</form>
</div>

<div style="overflow-x:auto">
<table><thead><tr><th>ID</th><th>Name</th><th>Student ID</th><th>Class</th><th>Age</th><th>Contact</th><th>Actions</th></tr></thead><tbody>
<?php $res=$conn->query("SELECT * FROM students"); while($r=$res->fetch_assoc()): ?>
<tr>
<td><?php echo $r['id']; ?></td><td><?php echo $r['name']; ?></td><td><?php echo $r['student_id']; ?></td><td><?php echo $r['class']; ?></td><td><?php echo $r['age']; ?></td><td><?php echo $r['contact']; ?></td>
<td>
<button class="action-btn edit" onclick="editStud(<?php echo htmlspecialchars(json_encode($r)); ?>)">Edit</button>
<a href="portal.php?del_student=<?php echo $r['id']; ?>" onclick="return confirm('Delete?')" class="action-btn del">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody></table>
</div>
</div>

<!-- GRADES TAB -->
<div id="grades-tab" style="display:none">
<h3>Add Grade (CREATE)</h3>
<div class="form-inline">
<form method="POST" style="display:flex;flex-wrap:wrap;gap:10px;width:100%">
<select name="grade_student" required style="flex:1;min-width:150px">
<option value="">Select Student</option>
<?php $res=$conn->query("SELECT * FROM students"); while($r=$res->fetch_assoc()){ echo "<option value='{$r['id']}'>{$r['name']} - {$r['student_id']}</option>"; } ?>
</select>
<input type="text" name="subject" placeholder="Subject" required style="flex:1">
<input type="number" name="score" placeholder="Score 0-100" required style="flex:1">
<input type="text" name="term" placeholder="Term e.g 1st Term" style="flex:1">
<button name="save_grade" class="btn">Add Grade</button>
</form>
</div>

<div style="overflow-x:auto">
<table><thead><tr><th>Student</th><th>Subject</th><th>Score</th><th>Term</th><th>Action</th></tr></thead><tbody>
<?php $res=$conn->query("SELECT g.*, s.name FROM grades g JOIN students s ON g.student_db_id=s.id"); while($r=$res->fetch_assoc()): ?>
<tr><td><?php echo $r['name']; ?></td><td><?php echo $r['subject']; ?></td><td><?php echo $r['score']; ?></td><td><?php echo $r['term']; ?></td>
<td><a href="portal.php?del_grade=<?php echo $r['id']; ?>" class="action-btn del">Delete</a></td></tr>
<?php endwhile; ?>
</tbody></table>
</div>
</div>

<!-- MESSAGES TAB -->
<div id="messages-tab" style="display:none">
<h3>Contact Messages (READ from DB)</h3>
<table><thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead><tbody>
<?php $res=$conn->query("SELECT * FROM messages ORDER BY id DESC"); while($r=$res->fetch_assoc()): ?>
<tr><td><?php echo $r['name']; ?></td><td><?php echo $r['email']; ?></td><td><?php echo $r['message']; ?></td><td><?php echo $r['date_sent']; ?></td></tr>
<?php endwhile; ?>
</tbody></table>
</div>

<?php else: // Student view only ?>
<h3>Your Academic Record (READ)</h3>
<table><thead><tr><th>Subject</th><th>Score</th><th>Term</th></tr></thead><tbody>
<?php $id=$_SESSION['db_id']; $res=$conn->query("SELECT * FROM grades WHERE student_db_id=$id"); while($r=$res->fetch_assoc()): ?>
<tr><td><?php echo $r['subject']; ?></td><td><?php echo $r['score']; ?></td><td><?php echo $r['term']; ?></td></tr>
<?php endwhile; ?>
</tbody></table>
<?php endif; ?>

<?php endif; ?>
</section>

<script>
function showTab(name){
  document.getElementById('students-tab').style.display=name=='students'?'block':'none';
  document.getElementById('grades-tab').style.display=name=='grades'?'block':'none';
  document.getElementById('messages-tab').style.display=name=='messages'?'block':'none';
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  event.target.classList.add('active');
}
function editStud(r){
  document.getElementById('edit_id').value=r.id;
  document.getElementById('sname').value=r.name;
  document.getElementById('ssid').value=r.student_id;
  document.getElementById('sclass').value=r.class;
  document.getElementById('sage').value=r.age;
  document.getElementById('scontact').value=r.contact;
  document.getElementById('spass').value=r.password;
  window.scrollTo(0,300);
}
</script>
<footer>&copy; 2026 JODASAM Academy | PHP & MySQL Version</footer>
</body></html>