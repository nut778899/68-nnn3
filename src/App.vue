<template>
  <nav class="navbar navbar-expand-lg" style="background-color: #86bfe7ff;">
    <div class="container">
      <a class="navbar-brand fw-bold text-white" href="/">MK Shop</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <!-- แสดงเฉพาะเมื่อเข้าสู่ระบบแล้ว -->
          <template v-if="isLoggedIn">  
            <li class="nav-item">
              <router-link class="nav-link" to="/orders">Orders</router-link>
            </li>

            <li class="nav-item">
              <router-link class="nav-link" to="/show_orders">Orders Detail</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/categorys">Categorys</router-link>
            </li>
              <li class="nav-item">
              <router-link class="nav-link" to="/product_edit">Add Product</router-link>
            </li>
            <li class="nav-item">
  <a class="nav-link" href="/report">Report</a>
</li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="#" @click="logout">Logout</a>
            </li>
          </template>

          <!-- แสดงเฉพาะเมื่อยังไม่ได้เข้าสู่ระบบ -->
          <template v-else>
            <li class="nav-item">
              <router-link class="nav-link" to="/">Menu</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/login">Login</router-link>
            </li>
          
          </template>

        </ul>

        <form class="d-flex" role="search" v-if="isLoggedIn">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>

  <!-- แสดงหน้าต่างเนื้อหา -->
  <router-view />
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      isLoggedIn: false,
    };
  },
  mounted() {
    // ตรวจสอบสถานะเมื่อโหลดหน้า
    this.checkLogin();
  },
  methods: {
    checkLogin() {
      this.isLoggedIn = localStorage.getItem("adminLogin") === "true";
    },
    logout() {
      if (confirm("ต้องการออกจากระบบหรือไม่?")) {
        // เคลียร์ข้อมูลทั้งหมดที่เกี่ยวข้องกับการล็อกอิน
        localStorage.removeItem("adminLogin");
        localStorage.removeItem("username");
        localStorage.removeItem("token");
        this.isLoggedIn = false;

        // กลับไปหน้าเมนูหลัก
        this.$router.push("/");
      }
    },
  },
  watch: {
    // เมื่อเปลี่ยนเส้นทาง ให้ตรวจสอบสถานะการล็อกอินใหม่
    $route() {
      this.checkLogin();
    },
  },
};
</script>


<style scoped>
.navbar {
  background-color: #86bfe7ff !important;
}
.nav-link {
  color: white !important;
  font-weight: 500;
}
.nav-link:hover {
  text-decoration: underline;
}
</style>
