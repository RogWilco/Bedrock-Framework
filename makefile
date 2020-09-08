path.src=src
path.lib=$(path.src)/lib
path.dist=dist

clean:
	rm -rf $(path.dist)

dist:
	mkdir $(path.dist)
	cp -r $(path.lib)/* $(path.dist)

dist.phar:
	mkdir $(path.dist)
	zip -r $(path.dist)/bedrock.phar $(path.lib)/*