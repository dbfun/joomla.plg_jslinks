COM_NAME = plg_jslinks
ARCHIVE_FNAME = archive/$(COM_NAME)_$(shell date +%Y%m%d_%H%M).tar.bz2

.PHONY: package archive help

package:
	test -d build || mkdir build
	zip -r build/$(COM_NAME).zip $(COM_NAME) -x \*.swp -q --archive-comment < README.md
	@echo "Joomla pacakage can be found in ./build directory."

archive:
	test -d archive || mkdir archive
	tar --exclude \*.swp -jcf $(ARCHIVE_FNAME) $(COM_NAME)
	@echo -e "\n\nYou can find the tarball at $(ARCHIVE_FNAME)"

help:
	@echo "Available Targets: "
	@echo "package - Create Joomla component package."
	@echo "archive - Create backup of component src to archive folder"
	@echo "clean - clean build directory"

# Default target is package
.DEFAULT_GOAL := package
