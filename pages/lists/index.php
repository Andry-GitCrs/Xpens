<?php 
  require_once __DIR__ . '/../../config/db.php';
  if(!isset($_SESSION['user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
  }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Lists · Xpens</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../../assets/js/tailwind.js"></script>
  <script>
    tailwind.config = { darkMode: 'class' }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer">
  <style>
    .loader{height:1rem;border-radius:9999px;background:#e5e7eb;width:100%;}
    .dark .loader{background:#374151;}
    .toast{position:fixed;top:1rem;right:1rem;max-width:20rem;padding:.75rem 1rem;color:#fff;border-radius:.5rem;font-size:.875rem;z-index:50;transition:transform .3s;transform:translateX(110%);}
    .toast.show{transform:translateX(0);}
    .toast.success{background:#10b981;} .toast.error{background:#ef4444;}
  </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100">

<div class="min-h-screen lg:flex">
  <!-- Sidebar -->
  <aside class="bg-white dark:bg-slate-800 w-full lg:w-64 border-r border-slate-200 dark:border-slate-700 p-4 flex flex-col justify-between">
    <!-- Top Section: Logo + Main Navigation -->
    <div>
      <h2 class="text-xl font-bold flex items-center gap-2 mb-4">
        <i class="fas fa-wallet text-indigo-600"></i><span><span class="text-yellow-500">X</span>pens</span>
      </h2>
      <nav class="space-y-2">
        <a href="/pages/dashboard" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
          <i class="fas fa-chart-line text-indigo-600"></i>
          <span>Dashboard</span>
        </a>
        <a href="/pages/lists" class="bg-slate-100 dark:bg-slate-700 flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
          <i class="fas fa-list-ul text-orange-600"></i>
          <span>Lists</span>
        </a>
        <a href="/pages/purchases" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
          <i class="fas fa-shopping-cart text-green-600"></i>
          <span>Purchases</span>
        </a>
        <a href="/pages/products" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
          <i class="fas fa-box-open text-blue-600"></i>
          <span>Products</span>
        </a>
      </nav>
    </div>

    <!-- Bottom Section: Extra Links -->
    <div class="space-y-2 pt-4 mt-6 border-t border-slate-200 dark:border-slate-700">
      <a href="#" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <i class="fas fa-leaf text-green-600"></i>
        <span>Saving</span>
        <span class="ml-auto text-xs font-semibold text-yellow-500 rounded-full px-2 py-0.5 flex items-center gap-1"><i class="fas fa-gem"></i> <span class="text-[10px]">pro</span> </span>
      </a>

      <a href="#" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <i class="fas fa-money-check-dollar text-blue-600"></i>
        <span>Bills</span>
        <span class="ml-auto text-xs font-semibold text-yellow-500 rounded-full px-2 py-0.5 flex items-center gap-1"><i class="fas fa-gem"></i> <span class="text-[10px]">pro</span> </span>
      </a>
      <a href="#settings" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <i class="fas fa-cog text-gray-600"></i>
        <span>Settings</span>
      </a>
      <a href="mailto: andrianaivonoe403@gmail.com" class="flex items-center gap-3 p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <i class="fas fa-bug text-yellow-600"></i>
        <span>Bug Report</span>
      </a>
      <button id="logoutBtn" class="w-full flex items-center gap-3 p-2 rounded text-left hover:bg-slate-100 dark:hover:bg-slate-700 text-indigo-500">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
    </div>
  </aside>

  <!-- Main content -->
  <div class="flex-1 overflow-y-auto max-h-screen">
    <!-- Topbar -->
    <header class="sticky top-0 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700 z-10">
      <div class="px-4 sm:px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Shopping Lists</h1>
        <div class="flex items-center gap-2">
          <button id="addBtn" class="me-3 bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold">+ New list</button>
          <a href="/pages/profile" class="flex items-center gap-3 p-2 px-3 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700">
            <i class="fas fa-user-tie ps-[2px]"></i>
            <span id="profile">—</span>
          </a>
          <button id="themeToggle" class="w-[40px] h-[40px] p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700">
            <i class="fas fa-moon"></i>
          </button>
        </div>
      </div>
    </header>

    <main class="p-4 sm:p-6 grid gap-6">
      <div id="listContainer" class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-x-auto p-3">
        <div class="loader"></div>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/40 hidden place-items-center z-30">
  <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-sm">
    <h2 id="modalTitle" class="font-semibold mb-3">Create List</h2>
    <input id="listName" placeholder="List name" class="w-full mb-3 px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-transparent">
    <textarea id="listDesc" placeholder="Description (optional)" class="w-full mb-3 px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-transparent resize-none"></textarea>
    <div class="flex justify-end space-x-2">
      <button id="cancelBtn" class="text-sm text-slate-500">Cancel</button>
      <button id="saveBtn" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-sm">Save</button>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>

<script>
const BASE='/api';
const toast=(m,t='success')=>{const el=document.getElementById('toast');el.textContent=m;el.className=`toast ${t}`;setTimeout(()=>el.classList.add('show'),10);setTimeout(()=>el.classList.remove('show'),3000);};
let lists=[], editing=null;

(async () => {
  const [total_expense] = await Promise.all([fetch(`${BASE}/purchases?get_total_expense=1`).then(r => r.json())]);
   document.getElementById('profile').textContent = total_expense.user.username;
})()


const fetchLists=async()=>{
  lists=await fetch(`${BASE}/lists`).then(r=>r.ok?r.json():Promise.reject());
  render();
};
const render=()=>{
  const c=document.getElementById('listContainer');

  if(lists.length===0){
    c.innerHTML='<h2 class="text-center text-slate-400 dark:text-slate-500"> Nothing to show </h2>';
    return;
  }

  console.log(lists);

  c.innerHTML = `
  <div class="flex flex-col md:flex-row justify-between items-center gap-2 mb-3">
    <input
      id="searchInput"
      type="text"
      placeholder="Search products..."
      class="px-3 py-1 w-full md:w-1/3 rounded bg-transparent text-gray-900 dark:text-white border border-slate-300 dark:border-slate-600 outline-none"
    />
    <select
      id="sortSelect"
      class="px-3 py-2 rounded bg-white dark:bg-slate-700 text-gray-900 dark:text-white dark:border-slate-600 outline-none"
    >
      <option value="updated_at">Sort by: Updated (newest)</option>
      <option value="created_at">Created (newest)</option>
      <option value="total_expense">Total Expense (high → low)</option>
      <option value="purchase_nbr">Purchases (most → fewest)</option>
    </select>
  </div>
  <table class="w-full text-sm">
    <thead class="border-b border-slate-200 dark:border-slate-700">
      <tr>
        <th class="p-2 text-left">List</th>
        <th class="p-2 text-left">Description</th>
        <th class="p-2 text-right">Total Expense <span class="text-slate-400 text-xs">(Ar)</span></th>
        <th class="p-2 text-right">Purchases</th>
        <th class="p-2 text-right">Created</th>
        <th class="p-2 text-right">Updated</th>
        <th class="p-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      ${lists.map((l, i) => `
        <tr class="border-b border-slate-100 dark:border-slate-700">
          <td class="p-2 font-medium">
            <button onclick="togglePurchases(${i})" class="text-blue-500 hover:underline">
              ${l.list_name}
            </button>
          </td>
          <td class="p-2 text-slate-500">${l.description || 'No description'}</td>
          <td class="p-2 text-right">${parseFloat(l.total_expense || 0).toLocaleString()}</td>
          <td class="p-2 text-right">${l.purchase_nbr} purchase${l.purchase_nbr > 1 ? 's' : ''}</td>
          <td class="p-2 text-right text-slate-400 text-xs">${new Date(l.created_at).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric'
          })}</td>
          <td class="p-2 text-right text-slate-400 text-xs">${new Date(l.updated_at).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric'
          })}</td>
          <td class="p-2 text-right space-x-1">
            <button onclick="editList(${l.id_list})" class="text-indigo-500 hover:underline" title="Edit">
              <i class="fas fa-edit"></i>
            </button>
            <button onclick="deleteList(${l.id_list})" class="text-red-500 hover:underline" title="Delete">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>

        <!-- Purchases Row (hidden by default) -->
        <tr id="purchases-${i}" class="hidden bg-slate-50 dark:bg-slate-800">
          <td colspan="7" class="p-2">
            ${(l.purchases?.length > 0) ? `
              <table class="w-full text-xs border border-slate-200 dark:border-slate-600 rounded">
                <thead>
                  <tr class="bg-slate-100 dark:bg-slate-700 text-left">
                    <th class="p-1">Product</th>
                    <th class="p-1">Description</th>
                    <th class="p-1 text-right">Number</th>
                    <th class="p-1">Unit</th>
                    <th class="p-1 text-right">Unit Price</th>
                    <th class="p-1 text-right">Total</th>
                    <th class="p-1 text-right">Date</th>
                  </tr>
                </thead>
                <tbody>
                  ${l.purchases.map(p => `
                    <tr class="border-t border-slate-200 dark:border-slate-700">
                      <td class="p-1">${p.product_name}</td>
                      <td class="p-1">${p.purchase_description}</td>
                      <td class="p-1 text-right">${parseFloat(p.number).toLocaleString()}</td>
                      <td class="p-1">${p.unit}</td>
                      <td class="p-1 text-right">${parseFloat(p.unit_price).toLocaleString()}</td>
                      <td class="p-1 text-right">${parseFloat(p.total_price).toLocaleString()}</td>
                      <td class="p-1 text-right text-slate-500">${new Date(p.purchase_date).toLocaleDateString(undefined, {
                        year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric'
                      })}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            ` : '<div class="text-slate-400 italic">No purchases</div>'}
          </td>
        </tr>
      `).join('')}
    </tbody>
  </table>
`;
};
const openModal = (isEdit = false) => {
  editing = isEdit;
  document.getElementById('modalTitle').textContent=isEdit?'Edit List':'New List';
  document.getElementById('listName').value=isEdit?lists.find(l=>l.id_list==isEdit).list_name:'';
  document.getElementById('listDesc').value=isEdit?(lists.find(l=>l.id_list==isEdit).description||''):'';
  document.getElementById('modal').classList.remove('hidden');
  document.getElementById('modal').classList.add('grid');
};
const closeModal=()=>{document.getElementById('modal').classList.add('hidden');document.getElementById('modal').classList.remove('grid');};

document.getElementById('addBtn').onclick=()=>openModal();
document.getElementById('cancelBtn').onclick=closeModal;
document.getElementById('saveBtn').onclick=async()=>{
  const name=document.getElementById('listName').value.trim();
  if(!name){toast('Name required','error');return;}
  const body={list_name:name,description:document.getElementById('listDesc').value.trim()};
  const method=editing?'PUT':'POST';
  const url=editing?`${BASE}/lists?id=${editing}`:`${BASE}/lists`;
  const res = await fetch(url,{method,headers:{'Content-Type':'application/json'},body:JSON.stringify(body)})
    .then(async res => {
      const response = await res.json();
      if(res.ok) {
        toast(editing?response.message || 'Updated':response.message || 'Created');
        closeModal();fetchLists();
        return
      }
      toast(editing?response.message || 'Update error':response.message || 'Creation error');
    });
};
function togglePurchases(index) {
  const row = document.getElementById(`purchases-${index}`);
  if (row) {
    row.classList.toggle('hidden');
  }
}
const editList=id=>openModal(id);
const deleteList=async id=>{
  if(!confirm('Delete?'))return;
  await fetch(`${BASE}/lists?id=${id}`,{method:'DELETE'});
  toast('Deleted');fetchLists();
};
fetchLists();
const darkMode = () => {
  document.documentElement.classList.toggle('dark', localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches));
};
darkMode();

document.getElementById('themeToggle').onclick = () => {
  localStorage.theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
  darkMode();
};
document.getElementById('logoutBtn').onclick = () => fetch(`${BASE}/auth/logout`).then(() => location.href = '/');

</script>
</body>
</html>