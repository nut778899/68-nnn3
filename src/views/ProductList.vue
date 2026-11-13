<template>
  <div class="container my-5">
    <h2 class="text-center mb-4">เมนูสินค้า</h2>

    <!-- 🔹 เลือกโต๊ะ -->
    <div class="mb-4 text-center">
      <label class="me-2 fw-bold">เลือกโต๊ะ:</label>
      <select v-model="selectedTable" class="form-select d-inline-block w-auto">
        <option disabled value="">-- เลือกโต๊ะ --</option>
        <option v-for="table in tables" :key="table" :value="table">
          โต๊ะ {{ table }}
        </option>
      </select>
    </div>

    <!-- 🔹 ปุ่มประเภทสินค้า -->
    <div class="text-center mb-4">
      <button
        v-for="category in categories"
        :key="category"
        class="btn me-2"
        :class="selectedCategory === category ? 'btn-primary' : 'btn-outline-primary'"
        @click="filterByCategory(category)"
      >
        {{ category }}
      </button>
      <button
        class="btn"
        :class="selectedCategory === 'ทั้งหมด' ? 'btn-success' : 'btn-outline-success'"
        @click="filterByCategory('ทั้งหมด')"
      >
        ทั้งหมด
      </button>
    </div>

    <!-- 🔹 แสดงสินค้า -->
    <div class="row">
      <div class="col-md-3" v-for="product in filteredProducts" :key="product.product_id">
        <div class="card shadow-sm mb-4">
          <img
            :src="'http://localhost/MK_SHOP/php_api/uploads/' + product.image"
            class="card-img-top"
            height="200"
            :alt="product.product_name"
          />
          <div class="card-body text-center">
            <h5 class="card-title">{{ product.product_name }}</h5>
            <p class="card-text">{{ product.price }} บาท</p>
            <button class="btn btn-success" @click="addToCart(product)">
              สั่งซื้อ
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔹 แสดงตะกร้าสินค้า -->
    <div class="mt-5">
      <h4>🧺 ตะกร้าสินค้า (โต๊ะ {{ selectedTable || '-' }})</h4>

      <table class="table table-bordered align-middle" v-if="cart.length > 0">
        <thead class="table-light">
          <tr>
            <th>สินค้า</th>
            <th>ราคา</th>
            <th style="width:150px;">จำนวน</th>
            <th>รวม</th>
            <th>ลบ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in cart" :key="index">
            <td>{{ item.product_name }}</td>
            <td>{{ item.price }}</td>
            <td class="text-center">
              <div class="d-flex justify-content-center align-items-center">
                <button
                  class="btn btn-sm btn-outline-secondary me-2"
                  @click="decreaseQty(item)"
                >
                  -
                </button>
                <span>{{ item.quantity }}</span>
                <button
                  class="btn btn-sm btn-outline-secondary ms-2"
                  @click="increaseQty(item)"
                >
                  +
                </button>
              </div>
            </td>
            <td>{{ (item.price * item.quantity).toFixed(2) }}</td>
            <td>
              <button
                class="btn btn-danger btn-sm"
                @click="removeFromCart(index)"
              >
                ลบ
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-end fw-bold">รวมทั้งหมด</td>
            <td colspan="2" class="fw-bold">{{ totalPrice.toFixed(2) }} บาท</td>
          </tr>
        </tfoot>
      </table>

      <div v-else class="text-muted">ยังไม่มีสินค้าในตะกร้า</div>

      <!-- ปุ่มยืนยันสั่งซื้อ -->
      <div class="text-end mt-3" v-if="cart.length > 0">
        <button class="btn btn-primary" @click="submitOrder">
          ยืนยันการสั่งซื้อ
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";

export default {
  name: "ProductList",
  setup() {
    const products = ref([]);
    const cart = ref([]);
    const selectedTable = ref("");
    const tables = [1, 2, 3, 4, 5];
    const loading = ref(true);
    const error = ref(null);

    // ✅ เพิ่มประเภทสินค้า
    const categories = ["เนื้อสัตว์", "ผัก", "เครื่องดื่ม" ];
    const selectedCategory = ref("ทั้งหมด");

    // ✅ ดึงข้อมูลสินค้า
    const fetchProducts = async () => {
      try {
        const response = await fetch("http://localhost/MK_SHOP/php_api/show_product.php");
        const result = await response.json();
        if (result.success) {
          products.value = result.data;
        } else {
          error.value = result.message;
        }
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // ✅ ฟังก์ชันกรองสินค้า
    const filteredProducts = computed(() => {
      if (selectedCategory.value === "ทั้งหมด") {
        return products.value;
      }
      return products.value.filter(
        (p) => p.category_name === selectedCategory.value
      );
    });

    const filterByCategory = (category) => {
      selectedCategory.value = category;
    };

    // ✅ เพิ่มสินค้าเข้าตะกร้า
    const addToCart = (product) => {
      if (!selectedTable.value) {
        alert("⚠️ กรุณาเลือกโต๊ะก่อนสั่งสินค้า");
        return;
      }

      const existing = cart.value.find(
        (item) => item.product_id === product.product_id
      );

      if (existing) {
        existing.quantity++;
      } else {
        cart.value.push({
          product_id: product.product_id,
          product_name: product.product_name,
          price: parseFloat(product.price),
          quantity: 1,
        });
      }

      alert(`✅ เพิ่ม "${product.product_name}" ลงในตะกร้าสำเร็จ!`);
    };

    const increaseQty = (item) => item.quantity++;
    const decreaseQty = (item) => {
      if (item.quantity > 1) item.quantity--;
      else if (confirm("ต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?")) {
        const index = cart.value.indexOf(item);
        if (index !== -1) cart.value.splice(index, 1);
      }
    };
    const removeFromCart = (index) => {
      if (confirm("ยืนยันการลบสินค้านี้หรือไม่?")) {
        cart.value.splice(index, 1);
      }
    };

    const totalPrice = computed(() =>
      cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
    );

    const submitOrder = async () => {
      if (!selectedTable.value) {
        alert("⚠️ กรุณาเลือกโต๊ะก่อนสั่งสินค้า");
        return;
      }

      if (cart.value.length === 0) {
        alert("⚠️ กรุณาเพิ่มสินค้าในตะกร้าก่อนสั่งซื้อ");
        return;
      }

      const orderData = {
        table_no: selectedTable.value,
        items: cart.value.map((item) => ({
          product_id: item.product_id,
          product_name: item.product_name,
          quantity: item.quantity,
          price: item.price,
        })),
        total: totalPrice.value,
      };

      try {
        const response = await fetch("http://localhost/MK_SHOP/php_api/order.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(orderData),
        });

        const result = await response.json();
        if (result.success) {
          alert("✅ สั่งซื้อสำเร็จ!");
          cart.value = [];
        } else {
          alert("❌ " + result.message);
        }
      } catch (error) {
        alert("เกิดข้อผิดพลาด: " + error.message);
      }
    };

    onMounted(fetchProducts);

    return {
      products,
      filteredProducts,
      categories,
      selectedCategory,
      filterByCategory,
      cart,
      selectedTable,
      tables,
      totalPrice,
      addToCart,
      increaseQty,
      decreaseQty,
      removeFromCart,
      submitOrder,
      loading,
      error,
    };
  },
};
</script>
