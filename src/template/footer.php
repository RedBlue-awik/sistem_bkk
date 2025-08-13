      <!--begin::Third Party Plugin(OverlayScrollbars)-->
      <script
          src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
          integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
          crossorigin="anonymous"></script>
      <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
      <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
      <!-- Bootstrap Bundle with Popper -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
      <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
      <script src="../../src/assets/js/adminlte.js"></script>
      <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
      <script>
          const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
          const Default = {
              scrollbarTheme: 'os-theme-light',
              scrollbarAutoHide: 'leave',
              scrollbarClickScroll: true,
          };
          document.addEventListener('DOMContentLoaded', function() {
              const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
              if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                  OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                      scrollbars: {
                          theme: Default.scrollbarTheme,
                          autoHide: Default.scrollbarAutoHide,
                          clickScroll: Default.scrollbarClickScroll,
                      },
                  });
              }
          });
      </script>
      <!--end::OverlayScrollbars Configure-->

      <!-- sortablejs -->
      <script
          src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
          integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
          crossorigin="anonymous"></script>
      <!-- sortablejs -->
      <script>
          const connectedSortables = document.querySelectorAll('.connectedSortable');
          connectedSortables.forEach((connectedSortable) => {
              let sortable = new Sortable(connectedSortable, {
                  group: 'shared',
                  handle: '.card-header',
              });
          });

          const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
          cardHeaders.forEach((cardHeader) => {
              cardHeader.style.cursor = 'move';
          });
      </script>
      <!-- apexcharts -->
      <script
          src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
          integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
          crossorigin="anonymous"></script>
      <!-- ChartJS -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
          // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
          // IT'S ALL JUST JUNK FOR DEMO
          // ++++++++++++++++++++++++++++++++++++++++++

          const sales_chart_options = {
              series: [{
                      name: 'Digital Goods',
                      data: [28, 48, 40, 19, 86, 27, 90],
                  },
                  {
                      name: 'Electronics',
                      data: [65, 59, 80, 81, 56, 55, 40],
                  },
              ],
              chart: {
                  height: 300,
                  type: 'area',
                  toolbar: {
                      show: false,
                  },
              },
              legend: {
                  show: false,
              },
              colors: ['#0d6efd', '#20c997'],
              dataLabels: {
                  enabled: false,
              },
              stroke: {
                  curve: 'smooth',
              },
              xaxis: {
                  type: 'datetime',
                  categories: [
                      '2023-01-01',
                      '2023-02-01',
                      '2023-03-01',
                      '2023-04-01',
                      '2023-05-01',
                      '2023-06-01',
                      '2023-07-01',
                  ],
              },
              tooltip: {
                  x: {
                      format: 'MMMM yyyy',
                  },
              },
          };

          const revenueChartEl = document.querySelector('#revenue-chart');
          if (revenueChartEl) {
              const sales_chart = new ApexCharts(
                  revenueChartEl,
                  sales_chart_options,
              );
              sales_chart.render();
          }
      </script>
      <script
          src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
          integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
          crossorigin="anonymous"></script>
      <script
          src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
          integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
          crossorigin="anonymous"></script>
      <!-- jsvectormap -->
      <script>
          const visitorsData = {
              US: 398, // USA
              SA: 400, // Saudi Arabia
              CA: 1000, // Canada
              DE: 500, // Germany
              FR: 760, // France
              CN: 300, // China
              AU: 700, // Australia
              BR: 600, // Brazil
              IN: 800, // India
              GB: 320, // Great Britain
              RU: 3000, // Russia
          };

          // World map by jsVectorMap (gunakan pengecekan agar tidak error)
          const worldMapEl = document.querySelector('#world-map');
          if (worldMapEl) {
              const map = new jsVectorMap({
                  selector: '#world-map',
                  map: 'world',
              });
          }
      </script>

      <!-- begin::SweetAlertKonfirmasi -->
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              // Seleksi semua tombol hapus
              const deleteButtons = document.querySelectorAll('.btn-hapus');

              deleteButtons.forEach(button => {
                  button.addEventListener('click', function(e) {
                      e.preventDefault(); // Mencegah langsung ke link

                      const adminId = this.dataset.id;
                      const href = this.getAttribute('href');

                      Swal.fire({
                          title: 'Yakin ingin menghapus?',
                          text: "Data akan hilang permanen!",
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#d33',
                          cancelButtonColor: '#3085d6',
                          confirmButtonText: 'Ya, hapus!',
                          cancelButtonText: 'Batal'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              // Redirect ke URL hapus
                              window.location.href = href;
                          }
                      });
                  });
              });
          });
      </script>
      <!-- end::SweetAlertKonfirmasi -->

      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
          // SweetAlert untuk button logout
          // Ambil semua elemen dengan class btn-logout
          document.querySelectorAll('.btn-logout').forEach(button => {
              button.addEventListener('click', function(e) {
                  e.preventDefault(); // Mencegah tautan langsung
                  const href = this.getAttribute('href'); // Ambil tautan href

                  Swal.fire({
                      title: 'Konfirmasi Logout',
                      text: "Apakah Anda yakin ingin logout?",
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Ya, Logout',
                      cancelButtonText: 'Batal'
                  }).then((result) => {
                      if (result.isConfirmed) {
                          // Arahkan ke tautan jika dikonfirmasi
                          window.location.href = href;
                      }
                  });
              });
          });
      </script>

      <script src="../../src/assets/js/jquery-3.7.1.min.js"></script>
      <!-- jQuery (paling atas) -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- DataTables core -->
      <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
      <!-- DataTables Responsive -->
      <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
      <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
      <script>
          $(document).ready(function() {
              $('#example').DataTable({
                  paging: true,
                  searching: true,
                  ordering: true,
                  info: true,
                  lengthMenu: [5, 10, 25, 50],
                  responsive: {
                      details: {
                          type: 'column',
                          target: 'tr'
                      }
                  },
                  columnDefs: [{
                          responsivePriority: 1,
                          targets: 2
                      }, // Nama 
                      {
                          responsivePriority: 1,
                          targets: 0
                      }, // No
                      {
                          responsivePriority: 3,
                          targets: 3
                      }, // Email
                      {
                          responsivePriority: 2,
                          targets: -1
                      } // Aksi
                  ],
                  language: {
                      sProcessing: "Sedang memproses...",
                      sLengthMenu: "Tampilkan _MENU_ data",
                      sZeroRecords: "Tidak ditemukan data yang sesuai",
                      sInfo: "Tampilkan _START_ sampai _END_ dari _TOTAL_ data",
                      sInfoEmpty: "Tampilkan 0 sampai 0 dari 0 data",
                      sInfoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                      sSearch: "Cari:",
                      oPaginate: {
                          sFirst: "Pertama",
                          sPrevious: "<-",
                          sNext: "->",
                          sLast: "Terakhir"
                      }
                  }
              });
          });
      </script>

      <!-- Tooltip -->
      <script>
          const tooltipTriggerList = document.querySelectorAll('[data-bs-trigger="hover"]')
          const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
      </script>

      <!-- Badge -->
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              // Fungsi untuk update badge
              function updateBadge() {
                  fetch('../../src/config/proses-pengumuman.php?t=' + Date.now(), {
                          cache: "no-store"
                      })
                      .then(response => response.json())
                      .then(data => {
                          const badges = document.querySelectorAll('.badgePengumuman');
                          badges.forEach(badge => {
                              if (data.jumlah > 0) {
                                  badge.classList.remove('d-none');
                                  badge.textContent = data.jumlah;
                                  badge.classList.add('notification-badge');
                              } else {
                                  badge.classList.add('d-none');
                                  badge.textContent = '0';
                                  badge.classList.remove('notification-badge');
                              }
                          });
                      });
              }

              // Update badge setiap 30 detik
              updateBadge();
              setInterval(updateBadge, 30000);

              // Tangani klik pada semua link pengumuman
              document.querySelectorAll('a[href*="pengumuman-all.php"]').forEach(link => {
                  link.addEventListener('click', function(e) {
                      // Kirim request untuk menandai sudah dilihat
                      fetch('../../src/config/proses-pengumuman.php?viewed=true&t=' + Date.now())
                          .then(() => updateBadge());

                      // Jika ini link di sidebar, biarkan navigasi tetap berjalan
                      if (this.closest('.app-sidebar')) {
                          return true;
                      }

                      // Jika di tempat lain, bisa ditambahkan logika khusus
                      e.preventDefault();
                      window.location.href = this.href;
                  });
              });
          });
      </script>
      <!-- end::Badge -->

      <!-- Alamat Maps -->
      <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
      <script>
          // Variabel global untuk map dan marker
          let map = null;
          let marker = null;
          let lastPosition = null;

          // Ambil elemen DOM (pastikan ada di halaman)
          const mapContainer = document.getElementById('map');
          const icon = document.querySelector('.lokasi-icon');
          const alamatInput = document.getElementById('alamat');
          const latInput = document.getElementById('lat');
          const lngInput = document.getElementById('lng');
          const suggestionsBox = document.getElementById('suggestions');

          // Fungsi inisialisasi map (dipanggil sekali saat dibutuhkan)
          function initMap() {
              if (!mapContainer || !alamatInput || !latInput || !lngInput) {
                  console.error('Elemen peta atau input tidak ditemukan!');
                  return;
              }

              if (map) return; // sudah diinisialisasi

              // Baca lat dan lng dari input, fallback ke Jakarta jika kosong
              const lat = parseFloat(latInput.value) || -6.2;
              const lng = parseFloat(lngInput.value) || 106.816666;

              map = L.map('map').setView([lat, lng], 13);

              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  maxZoom: 19
              }).addTo(map);

              marker = L.marker([lat, lng], {
                  draggable: true
              }).addTo(map);

              marker.on('dragend', function(e) {
                  const pos = e.target.getLatLng();
                  latInput.value = pos.lat;
                  lngInput.value = pos.lng;
                  reverseGeocode(pos.lat, pos.lng);
              });

              map.on('click', function(e) {
                  marker.setLatLng(e.latlng);
                  latInput.value = e.latlng.lat;
                  lngInput.value = e.latlng.lng;
                  reverseGeocode(e.latlng.lat, e.latlng.lng);
              });
          }

          // Fungsi update posisi marker dan peta
          function setMarkerAndView(lat, lon, popupText) {
              lastPosition = [lat, lon];
              if (marker) {
                  marker.setLatLng([lat, lon]);
              } else if (map) {
                  marker = L.marker([lat, lon], {
                      draggable: true
                  }).addTo(map);
              }
              if (popupText) {
                  marker.bindPopup(popupText).openPopup();
              } else if (marker) {
                  marker.closePopup();
              }
              if (map) {
                  map.setView([lat, lon], 17);
              }
          }

          // Fungsi ambil nama singkat dari hasil geocode
          function getShortName(item) {
              if (!item) return '';
              const a = item.address || {};
              const name = a.amenity || a.building || a.house || a.attraction || a.shop || a.tourism || a.name;
              if (name) return name;
              const road = a.road || a.pedestrian || a.cycleway;
              const locality = a.village || a.town || a.city || a.suburb || a.hamlet;
              if (road && locality) return road + ', ' + locality;
              if (road) return road;
              if (locality) return locality;
              if (item.display_name) return item.display_name.split(',')[0].trim();
              return '';
          }

          // Fungsi tampilkan suggestion hasil pencarian
          function showSuggestions(items) {
              if (!suggestionsBox) return;
              suggestionsBox.innerHTML = '';
              if (!items || items.length === 0) {
                  suggestionsBox.style.display = 'none';
                  return;
              }
              suggestionsBox.style.display = 'block';

              items.forEach((it, idx) => {
                  const div = document.createElement('div');
                  div.style.padding = '6px 10px';
                  div.style.cursor = 'pointer';
                  div.style.background = idx % 2 === 0 ? 'rgba(255,255,255,0.95)' : 'rgba(245,245,245,0.95)';

                  const short = getShortName(it) || it.display_name.split(',')[0];
                  const context = (it.address && (it.address.city || it.address.town || it.address.village)) ?
                      ' — ' + (it.address.city || it.address.town || it.address.village) : '';
                  div.textContent = short + context;

                  div.addEventListener('click', () => {
                      alamatInput.value = short;
                      latInput.value = it.lat;
                      lngInput.value = it.lon;
                      setMarkerAndView(it.lat, it.lon, short);
                      showSuggestions([]);
                  });

                  suggestionsBox.appendChild(div);
              });
          }

          // Fungsi reverse geocode (ambil alamat dari koordinat)
          async function reverseGeocode(lat, lon) {
              try {
                  const res = await fetch(`proxy-maps.php?lat=${lat}&lon=${lon}&addressdetails=1`);
                  if (!res.ok) throw new Error('Network response not ok');
                  const data = await res.json();
                  const short = getShortName(data) || (data.display_name ? data.display_name.split(',')[0] : '');
                  alamatInput.value = short || data.display_name || '';
              } catch (err) {
                  console.error('Reverse geocode error:', err);
              }
          }

          // Fungsi pencarian alamat via proxy
          async function searchAddress(q) {
              if (!q || q.length < 2) {
                  showSuggestions([]);
                  return;
              }
              try {
                  const res = await fetch(`proxy-maps.php?q=${encodeURIComponent(q)}&addressdetails=1&limit=5`);
                  if (!res.ok) throw new Error('Network response not ok');
                  const data = await res.json();
                  showSuggestions(data);
              } catch (err) {
                  console.error('Search error:', err);
                  showSuggestions([]);
              }
          }

          // Fungsi debounce untuk input search
          function debounce(fn, delay) {
              let timeoutId;
              return function(...args) {
                  clearTimeout(timeoutId);
                  timeoutId = setTimeout(() => fn.apply(this, args), delay);
              }
          }

          // Pasang event listener untuk input alamat
          if (alamatInput) {
              alamatInput.addEventListener('input', debounce(() => {
                  searchAddress(alamatInput.value.trim());
              }, 20));
          }

          // Pasang event listener untuk tombol icon (toggle map)
          if (icon && mapContainer) {
              icon.addEventListener('click', () => {
                  if (mapContainer.style.display === 'none' || mapContainer.style.display === '') {
                      mapContainer.style.display = 'block';
                      initMap();
                      if (map) map.invalidateSize();
                  } else {
                      mapContainer.style.display = 'none';
                  }
              });
          }

          // Tutup suggestion jika klik di luar
          document.addEventListener('click', (ev) => {
              if (!alamatInput || !suggestionsBox) return;
              if (!alamatInput.contains(ev.target) && !suggestionsBox.contains(ev.target)) {
                  suggestionsBox.style.display = 'none';
              }
          });

          // Fungsi untuk update lokasi dari tombol edit (kalau ada)
          function editLokasi(btn) {
              if (!btn) return;
              const alamat = btn.getAttribute('data-alamat') || '';
              const lat = parseFloat(btn.getAttribute('data-lat')) || 0;
              const lon = parseFloat(btn.getAttribute('data-lon')) || 0;

              if (alamatInput) alamatInput.value = alamat;
              if (latInput) latInput.value = lat;
              if (lngInput) lngInput.value = lon;

              setMarkerAndView(lat, lon, alamat);

              // Jika map belum inisialisasi, inisialisasi sekarang dan invalidate size
              if (!map) {
                  initMap();
                  if (map) map.invalidateSize();
              }
          }
      </script>