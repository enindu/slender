import fs from "fs/promises";
import path from "path";
import minify from "minify";

const types = [
  'admin/assets/css/',
  'admin/assets/js/',
  'user/assets/css/',
  // 'user/assets/js/',
  'template/assets/css/',
  'template/assets/js/'
];

types.forEach((type) => {
  fs.readdir(path.resolve(`sources/${type}`)).then((files) => {
    files.forEach((file) => {
      minify(path.resolve(`sources/${type + file}`)).then((content) => {
        fs.writeFile(path.resolve(`resources/${type + file}`), content);
      });
    });
  }).catch((error) => {
    console.log(error);
  }).finally(() => {
    console.log(`Files in ${type} directory are minified`);
  });
});
