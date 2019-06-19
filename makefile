#  * Order+
#  *
#  * Order+ is an extension for CMS Opencart 3.x Admin Panel.
#  * It extends displayed information in order list and e-mails, also adds product images in invoices, shopping lists and e-mails.
#  *
#  * @author		Andrii Burkatskyi aka underr underr.ua@gmail.com
#  * @copyright	Copyright (c) 2019 Andrii Burkatskyi
#  * @license		https://raw.githubusercontent.com/underr-ua/ocmod3-order-plus/master/EULA.txt End-User License Agreement
#  *
#  * @version		1.0
#  *
#  * @see			https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=37121
#  * @see			https://underr.space/notes/projects/project-017.html
#  * @see			https://github.com/underr-ua/ocmod3-order-plus


zip=$(shell basename `pwd`).ocmod.zip

license=EULA.txt
readme=README.md
datetime=201901010000.00
src=src
bin=bin


all: checkg clean makedir timestamp makezip hideg

checkg:
	@if [ ! -f "hideg.pwd" ]; then hideg; fi

makedir:
	mkdir -p "$(bin)"

timestamp:
	find . -exec touch -a -m -t $(datetime) {} \;

makezip:
	cd "$(src)" && zip -9qrX "../$(bin)/$(zip)" * && cd .. && zip -9qrX "$(bin)/$(zip)" "$(readme)" "$(license)"

hideg: hideg.pwd
	hideg "$(bin)/$(zip)"

clean:
	@echo Cleaning...
	@rm -f "$(bin)"/*.*
	@rm -f "$(src)"/*.zip
	@rm -f "$(src)/$(license)"
	@rm -f "$(src)/$(readme)"
