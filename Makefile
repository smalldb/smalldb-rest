
all: doc test

test:
	@ echo
	@ pear run-tests ./test
	@ echo

doc:
	make -C doc/ --no-print-dir


.PHONY: all test doc

