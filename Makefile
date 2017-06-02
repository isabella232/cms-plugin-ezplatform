# === Siteimprove Helper ===

# Styles
YELLOW=$(shell echo "\033[00;33m")
RED=$(shell echo "\033[00;31m")
RESTORE=$(shell echo "\033[0m")

# Variables
PHP_BIN := php
CURRENT_DIR := $(shell pwd)

.PHONY: list
list:
	@echo ""
	@echo "Simpeimprove plugin developer helper available targets:"
	@echo ""
	@echo "  $(YELLOW)convertpuml$(RESTORE)  > Convert PUML diagram in images"


.PHONY: convertpuml
convertpuml:
	java -jar plantuml.jar -o images/ docs/*.puml

