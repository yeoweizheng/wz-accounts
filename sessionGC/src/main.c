#include <stdio.h>
#include <stdlib.h>
#include <dirent.h>
#include <string.h>

int main(int argc, char *argv[]) {
	if(argc != 2){
		printf("Please specify session folder path\n");
		return(EXIT_FAILURE);
	}
	DIR *folder;
	struct dirent *file;
	folder = opendir(argv[1]);
	if(folder == NULL){
		printf("Unable to open folder\n");
		return(EXIT_FAILURE);
	}
	while((file = readdir(folder))){
		if(strcmp(file->d_name, "..") == 0 || strcmp(file->d_name, ".") == 0) continue;
		char *file_path = malloc((strlen(argv[1]) + strlen(file->d_name) +  1) * sizeof(char));
		strcpy(file_path, argv[1]);
		strcat(file_path, file->d_name);
		FILE *fp;
		fp = fopen(file_path, "r");
		fseek(fp, 0, SEEK_END);
		int file_size = ftell(fp);
		fseek(fp, 0, SEEK_SET);
		char* file_content = malloc((file_size + 1) * sizeof(char));
		fread(file_content, sizeof(char), file_size, fp);
		if(strstr(file_content, "username") == NULL) {
			remove(file_path);
		}
		free(file_path);
		free(file_content);
	}
	return(EXIT_SUCCESS);
}