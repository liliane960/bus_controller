<!-- simple example -->
<form action="register_process.php" method="POST">
  <label>Username:</label>
  <input type="text" name="username" required />
  
  <label>Password:</label>
  <input type="password" name="password" required />
  
  <label>Role:</label>
  <select name="role" required>
    <option value="admin">Admin</option>
    <option value="manager">Transport Manager</option>
    <option value="authority">Authority</option>
  </select>
  
  <button type="submit">Create User</button>
</form>
