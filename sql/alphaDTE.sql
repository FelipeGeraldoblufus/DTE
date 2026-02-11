c-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema dte
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema dte
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `dte` DEFAULT CHARACTER SET utf8 ;
USE `dte` ;

-- -----------------------------------------------------
-- Table `dte`.`empresa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`empresa` (
  `rut` VARCHAR(10) NOT NULL,
  `rznsoc` VARCHAR(100) NOT NULL,
  `giro` VARCHAR(80) NOT NULL,
  `telefono` VARCHAR(20) NULL,
  `correo` VARCHAR(80) NULL,
  `acteco` VARCHAR(6) NOT NULL,
  `direccion` VARCHAR(45) NULL,
  `comuna` VARCHAR(45) NULL,
  `ciudad` VARCHAR(45) NULL,
  `logo` VARCHAR(250) NULL,
  `fchresol` VARCHAR(12) NULL,
  `nroresol` INT NULL,
  PRIMARY KEY (`rut`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`cliente` (
  `rut` VARCHAR(10) NOT NULL,
  `rznsoc` VARCHAR(100) NOT NULL,
  `numid` VARCHAR(20) NULL,
  `nacionalidad` VARCHAR(3) NULL,
  `giro` VARCHAR(40) NOT NULL,
  `contacto` VARCHAR(80) NULL,
  `correo` VARCHAR(80) NULL,
  `direccion` VARCHAR(70) NULL,
  `comuna` VARCHAR(20) NULL,
  `ciudad` VARCHAR(20) NULL,
  `direccionpostal` VARCHAR(70) NULL,
  `comunapostal` VARCHAR(20) NULL,
  `ciudadpostal` VARCHAR(20) NULL,
  PRIMARY KEY (`rut`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`proveedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`proveedor` (
  `rut` VARCHAR(10) NOT NULL,
  `rznsoc` VARCHAR(100) NOT NULL,
  `numid` VARCHAR(20) NULL,
  `nacionalidad` VARCHAR(3) NULL,
  `giro` VARCHAR(40) NOT NULL,
  `contacto` VARCHAR(80) NULL,
  `correo` VARCHAR(80) NULL,
  `direccion` VARCHAR(70) NULL,
  `comuna` VARCHAR(20) NULL,
  `ciudad` VARCHAR(20) NULL,
  `direccionpostal` VARCHAR(70) NULL,
  `comunapostal` VARCHAR(20) NULL,
  `ciudadpostal` VARCHAR(20) NULL,
  PRIMARY KEY (`rut`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`documento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(10) NULL,
  `dte` VARCHAR(256) NULL,
  `folio` INT NULL,
  `emision` VARCHAR(45) NULL,
  `vencimiento` VARCHAR(45) NULL,
  `forma_pago` VARCHAR(45) NULL,
  `descuento` INT NULL,
  `exento` INT NULL,
  `iva` INT NULL,
  `otro_impuesto` INT NULL,
  `total` INT NULL,
  `empresa_rut` VARCHAR(10) NOT NULL,
  `cliente_rut` VARCHAR(10) NULL,
  `proveedor_rut` VARCHAR(10) NULL,
  PRIMARY KEY (`id`, `empresa_rut`),
  INDEX `fk_dte_empresa1_idx` (`empresa_rut` ASC),
  INDEX `fk_documento_cliente1_idx` (`cliente_rut` ASC),
  INDEX `fk_documento_proveedor1_idx` (`proveedor_rut` ASC),
  CONSTRAINT `fk_dte_empresa1`
    FOREIGN KEY (`empresa_rut`)
    REFERENCES `dte`.`empresa` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documento_cliente1`
    FOREIGN KEY (`cliente_rut`)
    REFERENCES `dte`.`cliente` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documento_proveedor1`
    FOREIGN KEY (`proveedor_rut`)
    REFERENCES `dte`.`proveedor` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`tipo_folio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`tipo_folio` (
  `tipo_numero` INT NOT NULL,
  `tipo_nombre` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`tipo_numero`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`folios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`folios` (
  `folio_actual` INT NOT NULL,
  `desde` INT NOT NULL,
  `hasta` INT NOT NULL,
  `vence` INT NOT NULL,
  `ruta` VARCHAR(250) NOT NULL,
  `empresa_rut` VARCHAR(10) NOT NULL,
  `tipo_folio` INT NOT NULL,
  PRIMARY KEY (`empresa_rut`, `tipo_folio`),
  INDEX `fk_folios_empresa1_idx` (`empresa_rut` ASC),
  INDEX `fk_folios_tipo_folio1_idx` (`tipo_folio` ASC),
  CONSTRAINT `fk_folios_empresa1`
    FOREIGN KEY (`empresa_rut`)
    REFERENCES `dte`.`empresa` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_folios_tipo_folio1`
    FOREIGN KEY (`tipo_folio`)
    REFERENCES `dte`.`tipo_folio` (`tipo_numero`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`permiso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`permiso` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `permiso` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`usuario` (
  `rut` VARCHAR(10) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NULL,
  `direccion` VARCHAR(45) NULL,
  `comuna` VARCHAR(45) NULL,
  `telefono` VARCHAR(45) NULL,
  `email` VARCHAR(100) NULL,
  `foto` VARCHAR(256) NULL,
  `permiso_id` INT NOT NULL,
  `empresa_rut` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`rut`, `permiso_id`, `empresa_rut`),
  INDEX `fk_usuario_permiso1_idx` (`permiso_id` ASC),
  INDEX `fk_usuario_empresa1_idx` (`empresa_rut` ASC),
  CONSTRAINT `fk_usuario_permiso1`
    FOREIGN KEY (`permiso_id`)
    REFERENCES `dte`.`permiso` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_empresa1`
    FOREIGN KEY (`empresa_rut`)
    REFERENCES `dte`.`empresa` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`region`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`region` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `region` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`provincia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`provincia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `provincia` VARCHAR(90) NOT NULL,
  `region_id` INT NOT NULL,
  PRIMARY KEY (`id`, `region_id`),
  INDEX `fk_provincia_region1_idx` (`region_id` ASC),
  CONSTRAINT `fk_provincia_region1`
    FOREIGN KEY (`region_id`)
    REFERENCES `dte`.`region` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`comuna`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`comuna` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comuna` VARCHAR(90) NOT NULL,
  `provincia_id` INT NOT NULL,
  PRIMARY KEY (`id`, `provincia_id`),
  INDEX `fk_comuna_provincia1_idx` (`provincia_id` ASC),
  CONSTRAINT `fk_comuna_provincia1`
    FOREIGN KEY (`provincia_id`)
    REFERENCES `dte`.`provincia` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`actividad_economica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`actividad_economica` (
  `codigo` INT NOT NULL,
  `actividad_economica` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`producto` (
  `codigo` VARCHAR(45) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `precio` INT NOT NULL,
  `descripcion` VARCHAR(500) NOT NULL,
  `unimed` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`firma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`firma` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rut` VARCHAR(10) NOT NULL,
  `nombre` VARCHAR(100) NULL,
  `fecha_desde` VARCHAR(45) NOT NULL,
  `fecha_hasta` VARCHAR(45) NOT NULL,
  `ruta` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`firma_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`firma_usuario` (
  `usuario_rut` VARCHAR(10) NOT NULL,
  `firma_id` INT NOT NULL,
  PRIMARY KEY (`usuario_rut`, `firma_id`),
  INDEX `fk_firma_usuario_usuario1_idx` (`usuario_rut` ASC),
  INDEX `fk_firma_usuario_firma1_idx` (`firma_id` ASC),
  CONSTRAINT `fk_firma_usuario_usuario1`
    FOREIGN KEY (`usuario_rut`)
    REFERENCES `dte`.`usuario` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_firma_usuario_firma1`
    FOREIGN KEY (`firma_id`)
    REFERENCES `dte`.`firma` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`bodega`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`bodega` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `bodega` VARCHAR(100) NOT NULL,
  `direccion` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`stock`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`stock` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `stock` VARCHAR(45) NOT NULL,
  `bodega_id` INT NOT NULL,
  `producto_codigo` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`, `bodega_id`, `producto_codigo`),
  INDEX `fk_stock_bodega1_idx` (`bodega_id` ASC),
  INDEX `fk_stock_producto1_idx` (`producto_codigo` ASC),
  CONSTRAINT `fk_stock_bodega1`
    FOREIGN KEY (`bodega_id`)
    REFERENCES `dte`.`bodega` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_stock_producto1`
    FOREIGN KEY (`producto_codigo`)
    REFERENCES `dte`.`producto` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`impuesto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`impuesto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `impuesto` VARCHAR(100) NOT NULL,
  `tasa` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`servicio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`servicio` (
  `codigo` VARCHAR(45) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `precio` INT NOT NULL,
  `descripcion` VARCHAR(500) NOT NULL,
  `unimed` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`banco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`banco` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `banco` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`tipo_cuenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`tipo_cuenta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`cuenta_cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`cuenta_cliente` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(56) NULL,
  `numero` INT NULL,
  `cliente_rut` VARCHAR(10) NOT NULL,
  `banco_id` INT NULL,
  `tipo_cuenta_id` INT NULL,
  PRIMARY KEY (`id`, `cliente_rut`),
  INDEX `fk_cuenta_cliente_cliente1_idx` (`cliente_rut` ASC),
  INDEX `fk_cuenta_cliente_banco1_idx` (`banco_id` ASC),
  INDEX `fk_cuenta_cliente_tipo_cuenta1_idx` (`tipo_cuenta_id` ASC),
  CONSTRAINT `fk_cuenta_cliente_cliente1`
    FOREIGN KEY (`cliente_rut`)
    REFERENCES `dte`.`cliente` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_cliente_banco1`
    FOREIGN KEY (`banco_id`)
    REFERENCES `dte`.`banco` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_cliente_tipo_cuenta1`
    FOREIGN KEY (`tipo_cuenta_id`)
    REFERENCES `dte`.`tipo_cuenta` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`cuenta_proveedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`cuenta_proveedor` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(56) NULL,
  `numero` INT NULL,
  `proveedor_rut` VARCHAR(10) NOT NULL,
  `banco_id` INT NULL,
  `tipo_cuenta_id` INT NULL,
  PRIMARY KEY (`id`, `proveedor_rut`),
  INDEX `fk_cuenta_proveedor_proveedor1_idx` (`proveedor_rut` ASC),
  INDEX `fk_cuenta_proveedor_banco1_idx` (`banco_id` ASC),
  INDEX `fk_cuenta_proveedor_tipo_cuenta1_idx` (`tipo_cuenta_id` ASC),
  CONSTRAINT `fk_cuenta_proveedor_proveedor1`
    FOREIGN KEY (`proveedor_rut`)
    REFERENCES `dte`.`proveedor` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_proveedor_banco1`
    FOREIGN KEY (`banco_id`)
    REFERENCES `dte`.`banco` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_proveedor_tipo_cuenta1`
    FOREIGN KEY (`tipo_cuenta_id`)
    REFERENCES `dte`.`tipo_cuenta` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`deuda`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`deuda` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `documento` VARCHAR(256) NULL,
  `num_doc` INT NULL,
  `emision` VARCHAR(40) NULL,
  `vencimiento` VARCHAR(40) NULL,
  `monto` INT NULL,
  `cliente_rut` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`, `cliente_rut`),
  INDEX `fk_deuda_cliente1_idx` (`cliente_rut` ASC),
  CONSTRAINT `fk_deuda_cliente1`
    FOREIGN KEY (`cliente_rut`)
    REFERENCES `dte`.`cliente` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`tipo_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`tipo_pago` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pago` VARCHAR(256) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`pago` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pago` VARCHAR(48) NULL,
  `monto` INT NULL,
  `fecha` VARCHAR(256) NULL,
  `comentario` VARCHAR(256) NULL,
  `tipo_pago_id` INT NOT NULL,
  `deuda_id` INT NOT NULL,
  `deuda_cliente_rut` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`, `tipo_pago_id`, `deuda_id`, `deuda_cliente_rut`),
  INDEX `fk_pago_tipo_pago1_idx` (`tipo_pago_id` ASC),
  INDEX `fk_pago_deuda1_idx` (`deuda_id` ASC, `deuda_cliente_rut` ASC),
  CONSTRAINT `fk_pago_tipo_pago1`
    FOREIGN KEY (`tipo_pago_id`)
    REFERENCES `dte`.`tipo_pago` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pago_deuda1`
    FOREIGN KEY (`deuda_id` , `deuda_cliente_rut`)
    REFERENCES `dte`.`deuda` (`id` , `cliente_rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`cuenta_empresa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`cuenta_empresa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(56) NULL,
  `numero` INT NULL,
  `empresa_rut` VARCHAR(10) NOT NULL,
  `banco_id` INT NULL,
  `tipo_cuenta_id` INT NULL,
  PRIMARY KEY (`id`, `empresa_rut`),
  INDEX `fk_cuenta_empresa_empresa1_idx` (`empresa_rut` ASC),
  INDEX `fk_cuenta_empresa_banco1_idx` (`banco_id` ASC),
  INDEX `fk_cuenta_empresa_tipo_cuenta1_idx` (`tipo_cuenta_id` ASC),
  CONSTRAINT `fk_cuenta_empresa_empresa1`
    FOREIGN KEY (`empresa_rut`)
    REFERENCES `dte`.`empresa` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_empresa_banco1`
    FOREIGN KEY (`banco_id`)
    REFERENCES `dte`.`banco` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cuenta_empresa_tipo_cuenta1`
    FOREIGN KEY (`tipo_cuenta_id`)
    REFERENCES `dte`.`tipo_cuenta` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`destino_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`destino_pago` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cuenta_empresa_id` INT NOT NULL,
  `pago_id` INT NOT NULL,
  `pago_tipo_pago_id` INT NOT NULL,
  PRIMARY KEY (`id`, `cuenta_empresa_id`, `pago_id`, `pago_tipo_pago_id`),
  INDEX `fk_destino_pago_cuenta_empresa1_idx` (`cuenta_empresa_id` ASC),
  INDEX `fk_destino_pago_pago1_idx` (`pago_id` ASC, `pago_tipo_pago_id` ASC),
  CONSTRAINT `fk_destino_pago_cuenta_empresa1`
    FOREIGN KEY (`cuenta_empresa_id`)
    REFERENCES `dte`.`cuenta_empresa` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_destino_pago_pago1`
    FOREIGN KEY (`pago_id` , `pago_tipo_pago_id`)
    REFERENCES `dte`.`pago` (`id` , `tipo_pago_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`empleado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`empleado` (
  `rut` VARCHAR(10) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NULL,
  `direccion` VARCHAR(45) NULL,
  `comuna` VARCHAR(45) NULL,
  `telefono` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `foto` VARCHAR(45) NULL,
  `empresa_rut` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`rut`, `empresa_rut`),
  INDEX `fk_usuario_empresa1_idx` (`empresa_rut` ASC),
  CONSTRAINT `fk_usuario_empresa10`
    FOREIGN KEY (`empresa_rut`)
    REFERENCES `dte`.`empresa` (`rut`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`detalle_documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`detalle_documento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(45) NULL,
  `nombre` VARCHAR(90) NULL,
  `descripcion` VARCHAR(256) NULL,
  `cantidad` INT NULL,
  `precio` INT NULL,
  `imp_adicional` INT NULL,
  `uni_med` VARCHAR(45) NULL,
  `descuento` INT NULL,
  `subtotal` INT NULL,
  `documento_id` INT NOT NULL,
  PRIMARY KEY (`id`, `documento_id`),
  INDEX `fk_detalle_documento_documento1_idx` (`documento_id` ASC),
  CONSTRAINT `fk_detalle_documento_documento1`
    FOREIGN KEY (`documento_id`)
    REFERENCES `dte`.`documento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`referencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`referencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `documento` VARCHAR(256) NULL,
  `folio` INT NULL,
  `fecha` VARCHAR(45) NULL,
  `descripcion` VARCHAR(256) NULL,
  `documento_id` INT NOT NULL,
  PRIMARY KEY (`id`, `documento_id`),
  INDEX `fk_referencia_documento1_idx` (`documento_id` ASC),
  CONSTRAINT `fk_referencia_documento1`
    FOREIGN KEY (`documento_id`)
    REFERENCES `dte`.`documento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dte`.`pago_documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dte`.`pago_documento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` VARCHAR(45) NULL,
  `monto` INT NULL,
  `glosa` VARCHAR(256) NULL,
  `documento_id` INT NOT NULL,
  PRIMARY KEY (`id`, `documento_id`),
  INDEX `fk_pago_documento1_idx` (`documento_id` ASC),
  CONSTRAINT `fk_pago_documento1`
    FOREIGN KEY (`documento_id`)
    REFERENCES `dte`.`documento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
