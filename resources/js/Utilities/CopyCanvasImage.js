import html2canvas from 'html2canvas';

export async function getScreenshotFromHTML () {
    html2canvas(document.querySelector('#screenshot-target')).then(async (canvas) => {
        canvas.toBlob(blob => navigator.clipboard.write([ new ClipboardItem({ 'image/png': blob }) ]));
    });
}
