all: doc

.PHONY: all clean doc figures

doc: figures
	cd .. && ./vendor/cascade/doc-tools/bin/build-documentation

figures:
	make -C figures/ --no-print-dir

clean:
	rm -rf doxygen/

