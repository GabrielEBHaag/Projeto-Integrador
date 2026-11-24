const api='../backend/api/index.php';let csrf='',products=[],suppliers=[],categories=[],catalogs={categorias:[],fornecedores:[]};
const $=id=>document.getElementById(id);const show=(id,on)=>$(id).hidden=!on;
function msg(text,error=false){$('notice').textContent=text;$('notice').className=error?'error':'ok'}
async function request(action,method='GET',data){const r=await fetch(`${api}?action=${action}`,{method,credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:data?JSON.stringify(data):undefined});const j=await r.json();if(!r.ok)throw Error(j.erro||'Erro na requisição');return j}
function data(form){return Object.fromEntries(new FormData(form).entries())}
function cell(text){const td=document.createElement('td');td.textContent=text??'';return td}
function table(target,heads,rows){const wrap=document.createElement('div');wrap.className='table-wrap';const t=document.createElement('table');const tr=document.createElement('tr');heads.forEach(h=>{const th=document.createElement('th');th.textContent=h;tr.append(th)});const thead=document.createElement('thead');thead.append(tr);t.append(thead);const tbody=document.createElement('tbody');rows.forEach(row=>{const tr=document.createElement('tr');row.forEach(x=>tr.append(cell(x)));tbody.append(tr)});t.append(tbody);wrap.append(t);$(target).replaceChildren(wrap)}
function options(select,items){select.replaceChildren();for(const x of items){const o=document.createElement('option');o.value=x.id;o.textContent=x.nome;select.append(o)}}
function renderProducts(){
 const wrap=document.createElement('div');wrap.className='table-wrap';const t=document.createElement('table');
 const thead=document.createElement('thead'),tr=document.createElement('tr');
 ['Produto','SKU','Categoria','Fornecedor','Saldo','Mínimo','Status','Ações'].forEach(h=>{const th=document.createElement('th');th.textContent=h;tr.append(th)});
 thead.append(tr);t.append(thead);const body=document.createElement('tbody');
 for(const p of products){const row=document.createElement('tr');
 [p.nome,p.sku,p.categoria,p.fornecedor,p.quantidade,p.estoque_minimo,Number(p.ativo)?'Ativo':'Inativo'].forEach(v=>row.append(cell(v)));
 const td=document.createElement('td');if(Number(p.ativo)){
 const edit=document.createElement('button');edit.type='button';edit.textContent='Editar';edit.onclick=()=>editProduct(p.id);
 const deactivate=document.createElement('button');deactivate.type='button';deactivate.textContent='Inativar';deactivate.onclick=()=>deactivateProduct(p.id);
 td.append(edit,document.createTextNode(' '),deactivate);
 }else{const reactivate=document.createElement('button');reactivate.type='button';reactivate.textContent='Reativar';reactivate.onclick=()=>reactivateProduct(p.id);td.append(reactivate)}row.append(td);body.append(row);
 }t.append(body);wrap.append(t);$('tabelaProdutos').replaceChildren(wrap);
}
function resetProductForm(){const form=$('produtoForm');form.reset();form.elements.id.value='';$('salvarProduto').textContent='Cadastrar produto';show('cancelarEdicao',false)}
function editProduct(id){const p=products.find(x=>Number(x.id)===Number(id));if(!p||!Number(p.ativo))return;
 const form=$('produtoForm');for(const key of ['id','nome','sku','categoria_id','fornecedor_id','estoque_minimo','preco_custo','descricao'])form.elements[key].value=p[key]??'';
 $('salvarProduto').textContent='Salvar alterações';show('cancelarEdicao',true);form.scrollIntoView({behavior:'smooth',block:'start'});form.elements.nome.focus();
}
async function deactivateProduct(id){const p=products.find(x=>Number(x.id)===Number(id));if(!p||!confirm(`Inativar o produto "${p.nome}"? O histórico será preservado.`))return;
 try{const result=await request('produto-inativar','POST',{id});msg(result.mensagem);if(Number($('produtoForm').elements.id.value)===Number(id))resetProductForm();await refresh()}catch(e){msg(e.message,true)}
}
async function reactivateProduct(id){const p=products.find(x=>Number(x.id)===Number(id));if(!p||!confirm(`Reativar o produto "${p.nome}"?`))return;
 try{const r=await request('produto-reativar','POST',{id});msg(r.mensagem);await refresh()}catch(e){msg(e.message,true)}
}
function resetSupplierForm(){const f=$('fornecedorForm');f.reset();f.elements.id.value='';$('salvarFornecedor').textContent='Cadastrar fornecedor';show('cancelarFornecedor',false)}
function editSupplier(id){const s=suppliers.find(x=>Number(x.id)===Number(id));if(!s||!Number(s.ativo))return;
 const f=$('fornecedorForm');for(const key of ['id','nome','cnpj','telefone','email','endereco'])f.elements[key].value=s[key]??'';
 $('salvarFornecedor').textContent='Salvar alterações';show('cancelarFornecedor',true);f.scrollIntoView({behavior:'smooth'});f.elements.nome.focus();
}
async function supplierStatus(id,activate){const s=suppliers.find(x=>Number(x.id)===Number(id));if(!s||!confirm(`${activate?'Reativar':'Inativar'} o fornecedor "${s.nome}"?`))return;
 try{const r=await request(activate?'fornecedor-reativar':'fornecedor-inativar','POST',{id});msg(r.mensagem);resetSupplierForm();await refresh()}catch(e){msg(e.message,true)}
}
function renderSuppliers(){const wrap=document.createElement('div');wrap.className='table-wrap';const t=document.createElement('table');const thead=document.createElement('thead');const h=document.createElement('tr');
 ['Nome','CNPJ','Telefone','E-mail','Status','Ações'].forEach(x=>{const th=document.createElement('th');th.textContent=x;h.append(th)});thead.append(h);t.append(thead);const tbody=document.createElement('tbody');
 for(const s of suppliers){const tr=document.createElement('tr');[s.nome,s.cnpj,s.telefone,s.email,Number(s.ativo)?'Ativo':'Inativo'].forEach(x=>tr.append(cell(x)));const td=document.createElement('td');
 if(Number(s.ativo)){const edit=document.createElement('button');edit.type='button';edit.textContent='Editar';edit.onclick=()=>editSupplier(s.id);td.append(edit,document.createTextNode(' '))}
 const status=document.createElement('button');status.type='button';status.textContent=Number(s.ativo)?'Inativar':'Reativar';status.onclick=()=>supplierStatus(s.id,!Number(s.ativo));td.append(status);tr.append(td);tbody.append(tr)}t.append(tbody);wrap.append(t);$('tabelaFornecedores').replaceChildren(wrap);
}
async function refresh(){const [p,c,h,fs,cs]=await Promise.all([request('produtos'),request('catalogos'),request('movimentacoes'),request('fornecedores'),request('categorias')]);products=p.produtos;catalogs=c;suppliers=fs.fornecedores;categories=cs.categorias;options(document.querySelector('[name=categoria_id]'),c.categorias);options(document.querySelector('[name=fornecedor_id]'),c.fornecedores);options(document.querySelector('[name=produto_id]'),products.filter(x=>Number(x.ativo)===1));const active=products.filter(x=>Number(x.ativo)===1),low=active.filter(x=>Number(x.quantidade)<=Number(x.estoque_minimo));$('stats').replaceChildren();for(const [label,value] of [['Produtos ativos',active.length],['Unidades em estoque',active.reduce((s,x)=>s+Number(x.quantidade),0)],['Abaixo/no mínimo',low.length]]){const d=document.createElement('div');d.className='stat';const strong=document.createElement('strong');strong.textContent=value;d.append(strong,document.createTextNode(label));$('stats').append(d)}table('alertas',['Produto','Quantidade','Mínimo'],low.map(x=>[x.nome,x.quantidade,x.estoque_minimo]));renderProducts();renderSuppliers();table('tabelaCategorias',['Nome','Descrição'],categories.map(x=>[x.nome,x.descricao]));table('tabelaHistorico',['Data','Produto','Tipo','Qtd.','Usuário','Observação'],h.movimentacoes.map(x=>[x.criado_em,x.produto,x.tipo,x.quantidade,x.usuario,x.observacao]));}
async function boot(){try{const me=await request('me');csrf=me.csrf;show('login',false);show('setup',false);show('app',true);show('logout',true);await refresh()}catch(e){if(e.message!=='Faça login para continuar')msg(e.message,true);show('app',false);show('logout',false);try{const s=await request('setup-status');show('setup',s.precisa_configurar);show('login',!s.precisa_configurar)}catch(x){msg(x.message,true)}}}
for(const b of document.querySelectorAll('nav button'))b.onclick=()=>{for(const x of document.querySelectorAll('.panel'))x.hidden=x.id!==b.dataset.tab;for(const x of document.querySelectorAll('nav button'))x.classList.toggle('active',x===b)};document.querySelector('nav button').click();
function submit(id,action,after){$(id).onsubmit=async e=>{e.preventDefault();try{const result=await request(action,'POST',data(e.target));msg(result.mensagem||'Salvo');e.target.reset();await after(result)}catch(x){msg(x.message,true)}}}
submit('setupForm','setup',async()=>{show('setup',false);show('login',true)});submit('loginForm','login',async r=>{csrf=r.csrf;await boot()});$('produtoForm').onsubmit=async e=>{e.preventDefault();const form=e.target;const values=data(form);const editing=Boolean(values.id);try{const result=await request(editing?'produto-atualizar':'produtos','POST',values);msg(result.mensagem);resetProductForm();await refresh()}catch(err){msg(err.message,true)}};
$('cancelarEdicao').onclick=resetProductForm;
$('fornecedorForm').onsubmit=async e=>{e.preventDefault();const values=data(e.target);try{const r=await request(values.id?'fornecedor-atualizar':'fornecedores','POST',values);msg(r.mensagem);resetSupplierForm();await refresh()}catch(err){msg(err.message,true)}};
$('cancelarFornecedor').onclick=resetSupplierForm;
submit('categoriaForm','categorias',refresh);submit('movForm','movimentacoes',refresh);
$('logout').onclick=async()=>{try{await request('logout','POST');csrf='';await boot()}catch(e){msg(e.message,true)}};boot();
