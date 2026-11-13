import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "productList",
    component: () => import("../views/ProductList.vue"),
  },

  {
    path: "/product_edit",
    name: "product_edit",
    component: () => import("../views/product_edit.vue"),
    meta: { requiresAuth: true },
  },
  
   
  {
    path: "/orders",
    name: "OrderList",
    component: () => import("../views/OrderList.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/report",
    name: "ReportChart",
    component: () => import("../views/ReportChart.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/show_orders",
    name: "show_orders",
    component: () => import("../views/Show_Order.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/login",
    name: "login",
    component: () => import("../views/login.vue"),
  },
  {
    path: "/report",
    name: "ReportChart",
    component: () => import("../views/ReportChart.vue"),
    meta: { requiresAuth: true },
  },
   {
    path: '/categorys',
    name: 'Categorys',
    component: () => import( '../views/Categorys.vue')
  }
  
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// 🧠 Navigation Guard — ตรวจสอบการเข้าสู่ระบบ
router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem("adminLogin") === "true";

  // ถ้าหน้านั้นต้องล็อกอินก่อน แต่ยังไม่ได้ล็อกอิน
  if (to.meta.requiresAuth && !isLoggedIn) {
    alert("⚠ กรุณาเข้าสู่ระบบก่อนใช้งานหน้านี้");
    next("/login");
  }
  // ถ้าเข้าสู่ระบบแล้วแต่พยายามกลับไปหน้า login อีก → ส่งกลับหน้าแรก
  else if (to.path === "/login" && isLoggedIn) {
    next("/");
  } 
  // อื่น ๆ ไปต่อได้ตามปกติ
  else {
    next();
  }
});

export default router;
