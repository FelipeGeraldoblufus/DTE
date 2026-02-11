<!-- BEGIN MENUBAR-->
<div id="menubar" class="menubar-inverse ">
 <div class="menubar-fixed-panel">
   <div>
     <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
       <i class="fa fa-bars"></i>
     </a>
   </div>
   <div class="expanded">
     <a href="<?php echo BASEURL ?>">
       <span class="text-lg text-bold text-primary ">DTE&nbsp;CHILE</span>
     </a>
   </div>
 </div>
 <div class="menubar-scroll-panel">      
   <!-- BEGIN MAIN MENU -->
   <ul id="main-menu" class="gui-controls">
     <li class="<?php if ($_SESSION['navMenu']== 'home'): ?> active<?php endif ?>">
       <a href="<?php echo BASEURL ?>home.php" >
         <div class="gui-icon"><i class="fa fa-home" aria-hidden="true"></i></div>
         <span class="title">Home</span>
       </a>
     </li>
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'company'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-industry" aria-hidden="true"></i></div>
         <span class="title">Empresa</span>
       </a>
       <ul>
         <li><a href="<?php echo BASEURL.'app/company/' ?>"><span class="title">Datos</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/users/' ?>"><span class="title">Usuarios</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/employes/' ?>"><span class="title">Empleados</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/maps/' ?>"><span class="title">Mapa Geopolitico</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/taxes/' ?>"><span class="title">Impuestos</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/accounts/' ?>"><span class="title">Cuentas</span></a></li>
         <li><a href="<?php echo BASEURL.'app/company/accountType/' ?>"><span class="title">Tipos de Cuentas</span></a></li>
       </ul>
     </li>
     
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'billing'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></div>
         <span class="title">Facturación</span>
       </a>
       <ul>
         <li><a href="<?php echo BASEURL.'app/billing/emision/listadte' ?>"><span class="title">Documentos</span></a></li>
         <li><a href="<?php echo BASEURL.'app/billing/folio/' ?>"><span class="title">Folios</span></a></li>
         <li><a href="<?php echo BASEURL.'app/billing/firma/' ?>"><span class="title">Firma Electronica</span></a></li>
         <li class="gui-folder">
           <a>
             <span class="title">Emision de Documentos</span>
           </a>
           <ul>
             <li><a href="<?php echo BASEURL.'app/billing/emision/boletaE/indexA.php' ?>">
               <span class="title">Boleta Electrónica</span>
             </a></li>
             <li><a href="<?php echo BASEURL.'app/billing/emision/factura/' ?>">
               <span class="title">Factura Electrónica</span>
             </a></li>
             <li><a href="<?php echo BASEURL.'app/billing/emision/factura-exenta/' ?>">
               <span class="title">Factura Electrónica Exenta</span>
             </a></li>
             <li><a href="<?php echo BASEURL.'app/billing/emision/guia-despacho/' ?>">
               <span class="title">Guía de Despacho Electrónica</span>
             </a></li>
             <li><a href="<?php echo BASEURL.'app/billing/emision/nota-debito/' ?>">
               <span class="title">Nota de Débito Electrónica</span>
             </a></li>
             <li><a href="<?php echo BASEURL.'app/billing/emision/nota-credito/' ?>">
               <span class="title">Nota de Crédito Electrónica</span>
             </a></li>
           </ul>
         </li>
       </ul>
     </li>
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'products'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
         <span class="title">Productos</span>
       </a>
       <ul>
         <li><a href="<?php echo BASEURL.'app/products/' ?>"><span class="title">Productos</span></a></li>
         <li><a href="<?php echo BASEURL.'app/products/bodegas' ?>"><span class="title">Bodegas</span></a></li>
         <li><a href="<?php echo BASEURL.'app/products/stocks' ?>"><span class="title">Stock</span></a></li>
       </ul>
     </li>
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'collections'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-balance-scale" aria-hidden="true"></i></div>
         <span class="title">Cobranzas</span>
       </a>
       <ul>
         <li><a href="#"><span class="title">Item</span></a></li>
       </ul>
     </li>
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'buys'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></div>
         <span class="title">Compras</span>
       </a>
       <ul>
         <li><a href="<?php echo BASEURL.'app/buys/provider/' ?>"><span class="title">Proveedores</span></a></li>
         <li><a href="<?php echo BASEURL.'app/buys/documents/' ?>"><span class="title">Ingresar Documentos</span></a></li>
       </ul>
     </li>
     <li class="gui-folder <?php if ($_SESSION['navMenu']== 'sales'): ?> active<?php endif ?>">
       <a>
         <div class="gui-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></div>
         <span class="title">Ventas</span>
       </a>
       <ul>
         <li><a href="<?php echo BASEURL.'app/sales/client/' ?>"><span class="title">Clientes</span></a></li>
         <li><a href="<?php echo BASEURL.'app/sales/documents/' ?>"><span class="title">Ingresar Documentos</span></a></li>
       </ul>
     </li>
     <li class="<?php if ($_SESSION['navMenu']== 'pdf'): ?> active<?php endif ?>">
       <a href="<?php echo BASEURL ?>app/pdf/" >
         <div class="gui-icon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
         <span class="title">Generador de PDF</span>
       </a>
     </li>
   </ul>
   <!-- END MAIN MENU -->
 </div>
</div>
<!-- END MENUBAR -->